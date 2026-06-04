<?php

namespace App\Controllers;

use App\Models\LandModel;
use App\Models\ActivityModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $activityModel = new ActivityModel();
        $userModel = new UserModel();
        $userRole = session()->get('role');
        $idKelompok = session()->get('id_kelompok');
        
        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $idKelompok;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }

        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

        $idUser = null; // Group-level view for all, even petani

        $summary = $landModel->getSummaryByKelompok($filterKelompok); // Don't filter by idUser for summary to see group stats
        
        $landBuilder = $landModel->builder();
        $landBuilder->select('id_lahan, id_kelompok, nama_lahan, komoditas, status_fase, luas, created_at, id_user');
        if ($filterArray) {
            $landBuilder->whereIn('id_kelompok', $filterArray);
        }
        $lands = $landBuilder->orderBy('created_at', 'DESC')->get()->getResultArray();

        // Enrich lands with harvest prediction and send notifications
        $notifModel = new \App\Models\NotificationModel();
        foreach ($lands as &$land) {
            $pred = $landModel->getHarvestPrediction($land['id_lahan']);
            $land['estimasi_panen'] = $pred;

            // Trigger notification if harvest time is due and it has a valid source
            if ($pred && $pred['hari_tersisa'] <= 0 && $pred['source'] === 'aktivitas_tanam') {
                $todayStr = date('Y-m-d');
                $title = "🌾 Waktu Panen: " . $land['nama_lahan'];
                
                // Avoid spamming: Check if we already sent this exact notification to this user today
                // Since we don't have a complex check, we'll see if the title exists for the user created today
                $db = \Config\Database::connect();
                $alreadySentUser = $db->table('notifications')
                                    ->where('id_user', $land['id_user'])
                                    ->where('judul', $title)
                                    ->like('created_at', $todayStr)
                                    ->countAllResults();

                if ($alreadySentUser == 0 && $land['id_user']) {
                    // Notify Petani
                    $notifModel->createNotification(
                        $land['id_user'], 
                        $title, 
                        "Estimasi masa panen untuk lahan {$land['nama_lahan']} (Komoditas: {$land['komoditas']}) telah tiba. Silakan persiapkan jadwal panen dan periksa kondisi lapangan.", 
                        'success'
                    );
                }

                // Notify PPL of the group
                $groupModel = new \App\Models\FarmerGroupModel();
                $group = $groupModel->find($land['id_kelompok']);
                if ($group && !empty($group['id_ppl'])) {
                    $alreadySentPpl = $db->table('notifications')
                                        ->where('id_user', $group['id_ppl'])
                                        ->where('judul', $title)
                                        ->like('created_at', $todayStr)
                                        ->countAllResults();
                    
                    if ($alreadySentPpl == 0) {
                        $notifModel->createNotification(
                            $group['id_ppl'], 
                            $title, 
                            "Lahan {$land['nama_lahan']} di kelompok {$group['nama_kelompok']} telah memasuki jadwal panen. Mohon berikan pendampingan kepada petani terkait.", 
                            'info'
                        );
                    }
                }
            }
        }

        // Get verification statistics
        $activityBuilder = $activityModel->builder();
        $activityBuilder->select('activities.*, lands.nama_lahan, users.nama as nama_petani');
        $activityBuilder->join('lands', 'lands.id_lahan = activities.id_lahan');
        $activityBuilder->join('users', 'users.id_user = activities.id_user');
        if ($filterArray) {
            $activityBuilder->whereIn('lands.id_kelompok', $filterArray);
        }
        $allActivities = $activityBuilder->get()->getResultArray();

        $verificationStats = [
            'disetujui' => count(array_filter($allActivities, fn($a) => $a['status'] === 'approved')),
            'menunggu' => count(array_filter($allActivities, fn($a) => $a['status'] === 'pending')),
            'ditolak' => count(array_filter($allActivities, fn($a) => $a['status'] === 'rejected'))
        ];
 
        // Get recent activities (for the timeline)
        $recentActivitiesBuilder = $activityModel->builder();
        $recentActivitiesBuilder->select('activities.*, users.nama as nama_petani, lands.nama_lahan');
        $recentActivitiesBuilder->join('users', 'users.id_user = activities.id_user');
        $recentActivitiesBuilder->join('lands', 'lands.id_lahan = activities.id_lahan');
        if ($filterArray) {
            $recentActivitiesBuilder->whereIn('lands.id_kelompok', $filterArray);
        }
        $recentActivities = $recentActivitiesBuilder->orderBy('activities.created_at', 'DESC')->limit(10)->get()->getResultArray();

        // Get pending activities (specifically for summary stats/other usage if needed)
        $pendingActivities = array_filter($recentActivities, fn($a) => $a['status'] === 'pending');
        
        $totalFarmers = 0;
        if ($userRole !== 'petani') {
            $farmerQuery = $userModel->where('role', 'petani');
            if ($filterArray) $farmerQuery->whereIn('id_kelompok', $filterArray);
            $totalFarmers = $farmerQuery->countAllResults();
        }
        
        // Get disaster alerts
        $disasterLands = $landModel->getDisasterLands($filterKelompok);
        
        // Get lands with GeoJSON for the map
        $farmerLandsGeoJSON = [];
        foreach ($lands as $land) {
            $geo = $landModel->getLandsGeoJSON($land['id_lahan']);
            if ($geo) $farmerLandsGeoJSON[] = $geo;
        }
        
        // Get Farmer Groups with stats for the leaderboard
        $groupModel = new \App\Models\FarmerGroupModel();
        $allGroups = $groupModel->findAll();
        $leaderboard = [];
        
        foreach ($allGroups as $group) {
            $groupStats = $landModel->getSummaryByKelompok($group['id_kelompok']);
            
            // Calculate real productivity for this group
            $groupProdQuery = $activityModel->builder();
            $groupProdQuery->select('SUM(activities.hasil_panen) as total_yield, SUM(lands.luas) as total_area');
            $groupProdQuery->join('lands', 'lands.id_lahan = activities.id_lahan');
            $groupProdQuery->where('lands.id_kelompok', $group['id_kelompok']);
            $groupProdQuery->where('activities.jenis_aktivitas', 'panen');
            $groupProdQuery->where('activities.status', 'approved');
            $groupProdData = $groupProdQuery->get()->getRowArray();
            
            $groupProd = ($groupProdData['total_area'] > 0) ? ($groupProdData['total_yield'] / $groupProdData['total_area']) : 0.0;

            $leaderboard[] = [
                'nama' => $group['nama_kelompok'],
                'kecamatan' => $group['kecamatan'],
                'total_luas' => $groupStats['total_luas'],
                'total_lands' => $groupStats['total_lands'],
                'prod' => $groupProd
            ];
        }
        // Sort by productivity
        usort($leaderboard, fn($a, $b) => $b['prod'] <=> $a['prod']);
        $leaderboard = array_slice($leaderboard, 0, 5);

        // Global Average Productivity
        $prodQuery = $activityModel->builder();
        $prodQuery->select('SUM(activities.hasil_panen) as total_yield, SUM(lands.luas) as total_area');
        $prodQuery->join('lands', 'lands.id_lahan = activities.id_lahan');
        $prodQuery->where('activities.jenis_aktivitas', 'panen');
        $prodQuery->where('activities.status', 'approved');
        if ($filterArray) $prodQuery->whereIn('lands.id_kelompok', $filterArray);
        $prodData = $prodQuery->get()->getRowArray();
        
        $avgProd = ($prodData['total_area'] > 0) ? ($prodData['total_yield'] / $prodData['total_area']) : 0.0;

        // Total Estimated Harvest for all lands
        $totalEstimatedYield = 0;
        foreach ($lands as $land) {
            $pred = $landModel->getHarvestPrediction($land['id_lahan']);
            if ($pred && isset($pred['total_yield'])) {
                $totalEstimatedYield += $pred['total_yield'];
            }
        }
        
        // Total Activities Count (Last 6 Months)
        $totalActivitiesCount = $activityModel->builder()
            ->join('lands', 'lands.id_lahan = activities.id_lahan')
            ->where('activities.tanggal >=', date('Y-m-d', strtotime('-6 months')));
        if ($filterArray) $totalActivitiesCount->whereIn('lands.id_kelompok', $filterArray);
        $totalActivitiesCount = $totalActivitiesCount->countAllResults();

        // Get Activity Trend (Last 6 Months)
        $monthlyProdData = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $label = date('M Y', strtotime("-$i months"));
            
            $mQuery = $activityModel->builder();
            $mQuery->select('COUNT(*) as total_act');
            $mQuery->join('lands', 'lands.id_lahan = activities.id_lahan');
            $mQuery->like('activities.tanggal', $month);
            if ($filterArray) $mQuery->whereIn('lands.id_kelompok', $filterArray);
            $mRes = $mQuery->get()->getRowArray();
            
            $monthlyProdData[] = (int)$mRes['total_act'];
            $monthlyLabels[] = $label;
        }

        // Get Weekly Trend (Last 7 Days)
        $weeklyData = [];
        $weeklyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i days"));
            $label = date('D', strtotime("-$i days"));
            
            $wQuery = $activityModel->builder();
            $wQuery->select('COUNT(*) as total_act');
            $wQuery->join('lands', 'lands.id_lahan = activities.id_lahan');
            $wQuery->where('activities.tanggal', $day);
            if ($filterArray) $wQuery->whereIn('lands.id_kelompok', $filterArray);
            $wRes = $wQuery->get()->getRowArray();
            
            $weeklyData[] = (int)$wRes['total_act'];
            $weeklyLabels[] = $label;
        }

        // Get Activity Distribution (by Farmer Group)
        $actQuery = $activityModel->builder();
        $actQuery->select('farmer_groups.nama_kelompok, COUNT(activities.id_aktivitas) as count');
        $actQuery->join('lands', 'lands.id_lahan = activities.id_lahan');
        $actQuery->join('farmer_groups', 'farmer_groups.id_kelompok = lands.id_kelompok');
        if ($filterArray) $actQuery->whereIn('lands.id_kelompok', $filterArray);
        $actQuery->groupBy('farmer_groups.nama_kelompok');
        $activityDist = $actQuery->get()->getResultArray();

        $distLabels = [];
        $distData = [];
        foreach ($activityDist as $row) {
            $distLabels[] = $row['nama_kelompok'];
            $distData[] = (int)$row['count'];
        }

        // Fallback for empty data
        if (empty($distLabels)) {
            $distLabels = ['Belum Ada Aktivitas'];
            $distData = [0];
        }

        $data = [
            'title' => 'Dashboard',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'summary' => $summary,
            'lands' => $lands,
            'landsGeoJSON' => $farmerLandsGeoJSON,
            'allActivities' => $allActivities,
            'verificationStats' => $verificationStats,
            'pendingActivities' => $pendingActivities,
            'totalFarmers' => $totalFarmers,
            'totalLands' => count($lands),
            'disasterLands' => $disasterLands,
            'leaderboard' => $leaderboard,
            'avgProd' => $avgProd,
            'totalEstimatedYield' => $totalEstimatedYield,
            'totalActivitiesCount' => $totalActivitiesCount,
            'recentActivities' => $recentActivities,
            'monthlyProdData' => $monthlyProdData,
            'monthlyLabels' => $monthlyLabels,
            'weeklyData' => $weeklyData,
            'weeklyLabels' => $weeklyLabels,
            'distLabels' => $distLabels,
            'distData' => $distData
        ];

        if ($userRole === 'petani') {
            return view('dashboard/petani', $data);
        }

        return view('dashboard/index', $data);
    }

    public function activities()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $activityModel = new \App\Models\ActivityModel();
        $userRole = session()->get('role');
        $idKelompok = session()->get('id_kelompok');

        // Get activities based on role
        if ($userRole === 'admin') {
            $activities = $activityModel->findAll();
        } else {
            $managedGroupIds = [$idKelompok];
            if ($userRole === 'ppl') {
                $groupModel = new \App\Models\FarmerGroupModel();
                $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
                $managedGroupIds = array_column($groups, 'id_kelompok');
                if (empty($managedGroupIds)) $managedGroupIds = [0];
            }
            
            // Get activities for user's farmer group lands
            $landModel = new LandModel();
            $lands = $landModel->whereIn('id_kelompok', $managedGroupIds)->findAll();
            $landIds = array_column($lands, 'id_lahan');
            
            if (!empty($landIds)) {
                $activities = $activityModel->whereIn('id_lahan', $landIds)->findAll();
            } else {
                $activities = [];
            }
        }

        $data = [
            'title' => 'Daftar Aktivitas',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'activities' => $activities
        ];

        return view('dashboard/activities', $data);
    }

    public function export($type)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $idKelompok = session()->get('id_kelompok');
        $userRole = session()->get('role');
        
        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $idKelompok;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;
        
        if ($type === 'lands') {
            $model = new \App\Models\LandModel();
            $data = [];
            if ($filterArray) {
                $data = $model->builder()->whereIn('id_kelompok', $filterArray)->get()->getResultArray();
            } else {
                $data = $model->findAll();
            }
            $filename = "Laporan_Lahan_" . date('Ymd') . ".csv";
            $header = ["ID", "Nama Lahan", "Komoditas", "Fase", "Luas (Ha)", "Tanggal Daftar"];
        } else {
            $model = new \App\Models\ActivityModel();
            // Fetch joined activities
            $builder = $model->builder();
            $builder->select('activities.*, users.nama as nama_petani, lands.nama_lahan');
            $builder->join('users', 'users.id_user = activities.id_user');
            $builder->join('lands', 'lands.id_lahan = activities.id_lahan');
            if ($filterArray) {
                $builder->whereIn('lands.id_kelompok', $filterArray);
            }
            $data = $builder->get()->getResultArray();
            $filename = "Laporan_Aktivitas_" . date('Ymd') . ".csv";
            $header = ["ID", "Petani", "Lahan", "Aktivitas", "Tanggal", "Status"];
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $header);

        foreach ($data as $row) {
            if ($type === 'lands') {
                fputcsv($output, [$row['id_lahan'], $row['nama_lahan'], $row['komoditas'], $row['status_fase'], $row['luas'], $row['created_at']]);
            } else {
                fputcsv($output, [$row['id_aktivitas'], $row['nama_petani'], $row['nama_lahan'], $row['jenis_aktivitas'], $row['tanggal'], $row['status']]);
            }
        }

        fclose($output);
        exit;
    }
}