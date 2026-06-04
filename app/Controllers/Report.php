<?php

namespace App\Controllers;

use App\Models\LandModel;
use App\Models\ActivityModel;
use App\Models\UserModel;

class Report extends BaseController
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
        $userId = session()->get('id_user');
        $idKelompokSession = session()->get('id_kelompok');

        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $idKelompokSession;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', $userId)->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }

        // Stats by Commodity
        $summary = $landModel->getSummaryByKelompok($filterKelompok);
        
        // Stats by Phase
        $db = \Config\Database::connect();
        $phaseStatsQuery = $db->table('lands')
                         ->select('status_fase, COUNT(*) as count');
        if ($filterKelompok !== null) {
            if (is_array($filterKelompok)) {
                $phaseStatsQuery->whereIn('id_kelompok', $filterKelompok);
            } else {
                $phaseStatsQuery->where('id_kelompok', $filterKelompok);
            }
        }
        $phaseStats = $phaseStatsQuery->groupBy('status_fase')->get()->getResultArray();

        // Activity Trends (last 6 months)
        $trendsQuery = $db->table('activities')
                     ->select("DATE_FORMAT(tanggal, '%Y-%m') as month, COUNT(*) as count")
                     ->join('lands', 'lands.id_lahan = activities.id_lahan')
                     ->where('tanggal >=', date('Y-m-01', strtotime('-5 months')));
        if ($filterKelompok !== null) {
            if (is_array($filterKelompok)) {
                $trendsQuery->whereIn('lands.id_kelompok', $filterKelompok);
            } else {
                $trendsQuery->where('lands.id_kelompok', $filterKelompok);
            }
        }
        $rawTrends = $trendsQuery->groupBy('month')->orderBy('month', 'ASC')->get()->getResultArray();

        // Pad the last 6 months with 0s to ensure the chart renders a continuous line
        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStr = date('Y-m', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months")); // e.g. May 2026
            
            $count = 0;
            foreach ($rawTrends as $row) {
                if ($row['month'] === $monthStr) {
                    $count = $row['count'];
                    break;
                }
            }
            
            $trends[] = [
                'month' => $monthName,
                'count' => $count
            ];
        }

        // Synchronize Top Farmer Groups with Database
        $topGroupsQuery = $db->table('farmer_groups fg')
            ->select('fg.nama_kelompok as nama, fg.kecamatan as desa, SUM(l.luas) as luas')
            ->select('IFNULL(SUM(a.hasil_panen), 0) as total_panen')
            ->join('lands l', 'l.id_kelompok = fg.id_kelompok', 'inner')
            ->join('activities a', "a.id_lahan = l.id_lahan AND a.jenis_aktivitas = 'panen' AND a.status = 'approved'", 'left');
            
        if ($filterKelompok !== null) {
            if (is_array($filterKelompok)) {
                $topGroupsQuery->whereIn('fg.id_kelompok', $filterKelompok);
            } else {
                $topGroupsQuery->where('fg.id_kelompok', $filterKelompok);
            }
        }
        
        $topGroupsRaw = $topGroupsQuery->groupBy('fg.id_kelompok')->get()->getResultArray();

        // Calculate productivity and sort
        foreach ($topGroupsRaw as &$group) {
            $group['prod'] = ($group['luas'] > 0) ? ($group['total_panen'] / $group['luas']) : 0;
        }

        // Sort by productivity DESC
        usort($topGroupsRaw, function($a, $b) {
            return $b['prod'] <=> $a['prod'];
        });

        $topGroups = [];
        $rank = 1;
        foreach (array_slice($topGroupsRaw, 0, 5) as $g) {
            $topGroups[] = [
                'rank' => $rank++,
                'nama' => $g['nama'],
                'desa' => $g['desa'],
                'luas' => $g['luas'],
                'prod' => (float)$g['prod']
            ];
        }

        // If no real data at all (no groups with lands)
        if (empty($topGroups)) {
             $topGroups = [];
        }

        $data = [
            'title' => 'Statistik & KPI Pertanian',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'summary' => $summary,
            'phaseStats' => $phaseStats,
            'trends' => $trends,
            'topGroups' => $topGroups
        ];

        return view('reports/index', $data);
    }
}
