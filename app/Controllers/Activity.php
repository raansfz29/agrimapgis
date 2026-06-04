<?php

namespace App\Controllers;

use App\Models\ActivityModel;
use App\Models\LandModel;

class Activity extends BaseController
{
    public function index($id = null)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $activityModel = new ActivityModel();
        $userRole = session()->get('role');
        $userIdKelompok = session()->get('id_kelompok');

        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $userIdKelompok;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

        // Pagination config
        $perPage = 10;
        $currentPage = (int)($this->request->getGet('page') ?? 1);
        if ($currentPage < 1) $currentPage = 1;
        $offset = ($currentPage - 1) * $perPage;

        // Optional filters
        $filterStatus = $this->request->getGet('status');
        $filterDate = $this->request->getGet('date');
        $filterType = $this->request->getGet('type');
        $filterVillage = $this->request->getGet('village');

        // Apply filters helper
        $applyFilters = function($b) use ($filterStatus, $filterDate, $filterType, $filterVillage) {
            if ($filterStatus) $b->where('activities.status', $filterStatus);
            if ($filterDate) $b->where('DATE(activities.created_at)', $filterDate);
            if ($filterType) $b->where('activities.jenis_aktivitas', $filterType);
            if ($filterVillage) {
                if ($filterVillage === 'RB Jaya') $b->like('lands.alamat', 'Jaya');
                elseif ($filterVillage === 'RB Raya') $b->like('lands.alamat', 'Raya');
                elseif ($filterVillage === 'Gedung Meneng') $b->like('lands.alamat', 'Meneng');
                else $b->like('lands.alamat', $filterVillage);
            }
        };

        // Count total for pagination
        $countBuilder = $activityModel->builder();
        $countBuilder->join('users', 'users.id_user = activities.id_user');
        $countBuilder->join('lands', 'lands.id_lahan = activities.id_lahan');
        if ($filterArray) {
            $countBuilder->whereIn('lands.id_kelompok', $filterArray);
        }
        $applyFilters($countBuilder);
        
        $totalActivities = $countBuilder->countAllResults();
        $totalPages = (int)ceil($totalActivities / $perPage);
        if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;

        // Fetch paginated activities with joins
        $builder = $activityModel->builder();
        $builder->select('activities.*, users.nama as nama_petani, lands.nama_lahan, lands.id_lahan as lahan_id');
        $builder->join('users', 'users.id_user = activities.id_user');
        $builder->join('lands', 'lands.id_lahan = activities.id_lahan');
        if ($filterArray) {
            $builder->whereIn('lands.id_kelompok', $filterArray);
        }
        $applyFilters($builder);
        
        $activities = $builder->orderBy('activities.created_at', 'DESC')
                              ->limit($perPage, $offset)
                              ->get()->getResultArray();

        // Handle selected activity for detail view
        $selectedActivity = null;
        if ($id) {
            $selectedActivity = $activityModel
                ->select('activities.*, users.nama as nama_petani, users.id_kelompok, lands.nama_lahan, lands.komoditas, ST_AsGeoJSON(koordinat, 6, 2) as geojson_koordinat')
                ->join('users', 'users.id_user = activities.id_user')
                ->join('lands', 'lands.id_lahan = activities.id_lahan')
                ->find($id);
        } else if (!empty($activities)) {
            $firstId = $activities[0]['id_aktivitas'];
            $selectedActivity = $activityModel
                ->select('activities.*, users.nama as nama_petani, users.id_kelompok, lands.nama_lahan, lands.komoditas, ST_AsGeoJSON(koordinat, 6, 2) as geojson_koordinat')
                ->join('users', 'users.id_user = activities.id_user')
                ->join('lands', 'lands.id_lahan = activities.id_lahan')
                ->find($firstId);
        }

        // Get group name for selected activity
        if ($selectedActivity) {
            $groupModel = new \App\Models\FarmerGroupModel();
            $group = $groupModel->find($selectedActivity['id_kelompok']);
            $selectedActivity['nama_kelompok'] = $group['nama_kelompok'] ?? 'Kelompok Tani';
        }

        // Stats for attention card
        $flaggedBuilder = $activityModel->builder();
        $flaggedBuilder->join('lands', 'lands.id_lahan = activities.id_lahan');
        $flaggedBuilder->where('activities.status', 'rejected');
        if ($filterArray) $flaggedBuilder->whereIn('lands.id_kelompok', $filterArray);
        $flaggedCount = $flaggedBuilder->countAllResults();

        $pendingBuilder = $activityModel->builder();
        $pendingBuilder->join('lands', 'lands.id_lahan = activities.id_lahan');
        $pendingBuilder->where('activities.status', 'pending');
        if ($filterArray) $pendingBuilder->whereIn('lands.id_kelompok', $filterArray);
        $pendingCount = $pendingBuilder->countAllResults();

        // Get Activity Distribution (by Farmer Group) for Chart
        $distQuery = $activityModel->builder();
        $distQuery->select('farmer_groups.nama_kelompok, COUNT(activities.id_aktivitas) as count');
        $distQuery->join('lands', 'lands.id_lahan = activities.id_lahan');
        $distQuery->join('farmer_groups', 'farmer_groups.id_kelompok = lands.id_kelompok');
        if ($filterArray) $distQuery->whereIn('lands.id_kelompok', $filterArray);
        $distQuery->groupBy('farmer_groups.nama_kelompok');
        $activityDist = $distQuery->get()->getResultArray();

        $distLabels = [];
        $distData = [];
        foreach ($activityDist as $row) {
            $distLabels[] = $row['nama_kelompok'];
            $distData[] = (int)$row['count'];
        }

        if (empty($distLabels)) {
            $distLabels = ['Belum Ada Data'];
            $distData = [0];
        }

        $data = [
            'title'           => 'Verifikasi Aktivitas',
            'nama'            => session()->get('nama'),
            'role'            => session()->get('role'),
            'activities'      => $activities,
            'selected'        => $selectedActivity,
            'currentPage'     => $currentPage,
            'totalPages'      => $totalPages,
            'totalActivities' => $totalActivities,
            'perPage'         => $perPage,
            'flaggedCount'    => $flaggedCount,
            'pendingCount'    => $pendingCount,
            'filterStatus'    => $filterStatus,
            'distLabels'      => $distLabels,
            'distData'        => $distData
        ];

        if ($userRole === 'petani') {
            $data['title'] = 'Aktivitas Saya';
            return view('activity/index', $data);
        }

        return view('activity/verification', $data);
    }

    public function input()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $userRole = session()->get('role');
        $userIdKelompok = session()->get('id_kelompok');

        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $userIdKelompok;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

        // Get lands based on role with GeoJSON
        $landBuilder = $landModel->builder();
        $landBuilder->select('lands.id_lahan, lands.id_kelompok, lands.nama_lahan, lands.komoditas, lands.status_fase, lands.luas, lands.latitude, lands.longitude, ST_AsGeoJSON(lands.geom, 6, 2) as geojson');
        if ($filterArray) {
            $landBuilder->whereIn('lands.id_kelompok', $filterArray);
        }
        $lands = $landBuilder->orderBy('lands.created_at', 'DESC')->get()->getResultArray();

        // Fetch farmers for group-level managers (ppl or admin) or farmers (petani)
        $userModel = new \App\Models\UserModel();
        $farmers = [];
        if ($userRole === 'admin') {
            $farmers = $userModel->where('role', 'petani')->findAll();
        } elseif ($filterArray) {
            $farmers = $userModel->where('role', 'petani')->whereIn('id_kelompok', $filterArray)->findAll();
        }

        $data = [
            'title'   => 'Input Aktivitas',
            'nama'    => session()->get('nama'),
            'role'    => session()->get('role'),
            'lands'   => $lands,
            'farmers' => $farmers
        ];

        return view('activity/input', $data);
    }

    public function save()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userRole = session()->get('role');

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'id_lahan' => 'required|integer',
                'jenis_aktivitas' => 'required|min_length[3]|max_length[50]',
                'tanggal' => 'required|valid_date',
                'dosis' => 'required|min_length[1]|max_length[100]',
                'metode' => 'required|min_length[1]|max_length[100]',
                'deskripsi' => 'required|min_length[1]|max_length[500]',
                'foto' => 'uploaded[foto]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]',
                'latitude' => 'permit_empty|decimal',
                'longitude' => 'permit_empty|decimal'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $activityModel = new ActivityModel();
            
            // Allow PPL/Admin/Petani to attribute activity to the selected farmer, default to session user
            $targetUserId = session()->get('id_user');
            if (in_array($userRole, ['ppl', 'admin', 'petani']) && $this->request->getPost('id_user')) {
                $targetUserId = $this->request->getPost('id_user');
            }

            $dosis = $this->request->getPost('dosis');
            $metode = $this->request->getPost('metode');
            $deskripsiInput = $this->request->getPost('deskripsi');
            $fullDeskripsi = "Dosis: {$dosis} | Metode: {$metode} | Catatan: {$deskripsiInput}";

            $data = [
                'id_lahan'        => $this->request->getPost('id_lahan'),
                'id_user'         => $targetUserId,
                'jenis_aktivitas' => $this->request->getPost('jenis_aktivitas'),
                'tanggal'         => date('Y-m-d'),
                'deskripsi'       => $fullDeskripsi,
                'status'          => 'pending',
            ];

            // Simpan hasil panen jika aktivitas panen
            $jenis = strtolower($this->request->getPost('jenis_aktivitas') ?? '');
            if (in_array($jenis, ['panen', 'pemanenan'])) {
                $hasilPanen = $this->request->getPost('hasil_panen');
                if ($hasilPanen !== null && $hasilPanen !== '') {
                    $data['hasil_panen'] = (float)$hasilPanen;
                    $data['satuan']      = $this->request->getPost('satuan') ?? 'Ton';
                }
            }


            // Handle file upload for foto
            $foto = $this->request->getFile('foto');
            if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                $newName = $foto->getRandomName();
                $foto->move(WRITEPATH . 'uploads', $newName);
                $data['foto'] = $newName;
            }

            // Handle coordinate if provided
            $latitude = $this->request->getPost('latitude');
            $longitude = $this->request->getPost('longitude');
            $geoResult = null;
            
            if (!empty($latitude) && !empty($longitude)) {
                $pointGeoJSON = json_encode([
                    'type' => 'Point',
                    'coordinates' => [(float)$longitude, (float)$latitude]
                ]);
                
                // Geofencing Check
                $landModel = new LandModel();
                $geoResult = $landModel->getGeofencingResult($data['id_lahan'], (float)$longitude, (float)$latitude);
                
                if (!$geoResult || ($geoResult['is_inside'] == 0 && $geoResult['distance'] > 100)) {
                    $dist = $geoResult ? round($geoResult['distance'], 1) : 'unknown';
                    
                    // Notify Farmer (Rejected due to Out of Bounds)
                    $notifModel = new \App\Models\NotificationModel();
                    $landModel2 = new LandModel();
                    $land2 = $landModel2->find($data['id_lahan']);
                    $namaLahan = $land2['nama_lahan'] ?? 'Lahan #' . $data['id_lahan'];

                    $notifModel->createNotification(
                        $targetUserId,
                        '❌ Aktivitas Ditolak (Luar Lahan)',
                        "Upaya input aktivitas '{$data['jenis_aktivitas']}' pada {$namaLahan} ditolak otomatis oleh sistem karena lokasi Anda berada di LUAR jangkauan lahan (jarak: {$dist} meter). Data tidak disimpan.",
                        'danger'
                    );

                    return redirect()->back()->withInput()->with('error', "Gagal menyimpan aktivitas. Lokasi Anda berada di luar jangkauan lahan (jarak: {$dist} meter).");
                }

                $insertedId = $activityModel->insertActivityWithPoint($data, $pointGeoJSON);
            } else {
                $insertedId = $activityModel->insert($data);
            }

            if ($insertedId) {
                $notifModel     = new \App\Models\NotificationModel();
                $farmerKelompok = (int)session()->get('id_kelompok');
                $farmerNama     = session()->get('nama');
                $landModel2     = new LandModel();
                $land2          = $landModel2->find($data['id_lahan']);
                $namaLahan      = $land2['nama_lahan'] ?? 'Lahan #' . $data['id_lahan'];

                // --- Notify all PPL in the same farmer group ---
                if ($data['status'] === 'pending') {
                    $notifModel->broadcastToGroup(
                        $farmerKelompok,
                        'ppl',
                        '🔔 Aktivitas Baru Menunggu Verifikasi',
                        "{$farmerNama} melaporkan aktivitas '{$data['jenis_aktivitas']}' pada lahan {$namaLahan}. Segera lakukan verifikasi.",
                        'warning'
                    );

                    // Notify Farmer (Accepted/Pending)
                    $msgTitle = (!empty($latitude) && !empty($longitude)) ? '✅ Lokasi Sesuai (Dalam Lahan)' : '✅ Aktivitas Disimpan';
                    $msgBody = (!empty($latitude) && !empty($longitude)) 
                        ? "Aktivitas '{$data['jenis_aktivitas']}' berhasil dicatat karena lokasi Anda berada di DALAM area lahan. Menunggu verifikasi PPL."
                        : "Aktivitas '{$data['jenis_aktivitas']}' berhasil dicatat. Menunggu verifikasi PPL.";

                    $notifModel->createNotification(
                        $targetUserId,
                        $msgTitle,
                        $msgBody,
                        'success'
                    );
                }

                $msg = 'Aktivitas berhasil disimpan dan menunggu approval PPL.';
                return redirect()->to('/activity')->with('success', $msg);
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan aktivitas.');
            }
        }

        return redirect()->to('/activity/input');
    }

    public function detail($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $activityModel = new ActivityModel();
        $landModel = new LandModel();
        
        // Get activity by ID with spatial coordinate as WKT
        $activity = $activityModel
            ->select('activities.*, ST_AsText(koordinat) as koordinat_wkt')
            ->find($id);
        
        if (!$activity) {
            return redirect()->to('/activity')->with('error', 'Aktivitas tidak ditemukan.');
        }

        // Check if user has permission to view this activity
        $userRole = session()->get('role');
        $userIdKelompok = session()->get('id_kelompok');
        
        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $userIdKelompok;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;
        
        if ($filterArray) {
            $land = $landModel->find($activity['id_lahan']);
            if (!$land || !in_array($land['id_kelompok'], $filterArray)) {
                return redirect()->to('/activity')->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
        }

        // Get land information
        $land = $landModel->find($activity['id_lahan']);
        
        // Get user information
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($activity['id_user']);

        $data = [
            'title' => 'Detail Aktivitas',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'activity' => $activity,
            'land' => $land,
            'user' => $user
        ];

        return view('activity/detail', $data);
    }

    public function approve($id)
    {
        if (!session()->get('is_logged_in') || !in_array(session()->get('role'), ['ppl', 'admin'])) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $activityModel = new ActivityModel();
        $activity = $activityModel->find($id);

        if (!$activity) {
            return redirect()->to('/activity/verification')->with('error', 'Aktivitas tidak ditemukan.');
        }

        // Update status to approved
        $updated = $activityModel->update($id, ['status' => 'approved']);

        if ($updated) {
            // Notify farmer: approved
            $notifModel = new \App\Models\NotificationModel();
            $notifModel->createNotification(
                $activity['id_user'],
                '✅ Aktivitas Disetujui PPL',
                "Aktivitas '{$activity['jenis_aktivitas']}' pada lahan Anda telah disetujui oleh PPL. Terus pertahankan prestasi Anda!",
                'success'
            );

            return redirect()->to('/activity/verification/' . $id)->with('success', 'Aktivitas berhasil disetujui.');
        } else {
            return redirect()->to('/activity/verification/' . $id)->with('error', 'Gagal menyetujui aktivitas.');
        }
    }

    public function reject($id)
    {
        if (!session()->get('is_logged_in') || !in_array(session()->get('role'), ['ppl', 'admin'])) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $activityModel = new ActivityModel();
        $activity = $activityModel->find($id);

        if (!$activity) {
            return redirect()->to('/activity/verification')->with('error', 'Aktivitas tidak ditemukan.');
        }

        // Append rejection note to deskripsi
        $note = trim($this->request->getPost('catatan_penolakan') ?? '');
        $newDeskripsi = $activity['deskripsi'];
        if ($note) {
            $newDeskripsi = rtrim($newDeskripsi) . "\n\n[CATATAN PENOLAKAN]: " . $note;
        }

        $updated = $activityModel->update($id, [
            'status'   => 'rejected',
            'deskripsi' => $newDeskripsi
        ]);

        if ($updated) {
            // Send notification to farmer
            $notifModel = new \App\Models\NotificationModel();
            $notifModel->createNotification(
                $activity['id_user'],
                'Aktivitas Ditolak',
                "Aktivitas '{$activity['jenis_aktivitas']}' pada lahan Anda ditolak. Catatan: " . ($note ?: 'Tidak ada catatan.'),
                'danger'
            );

            return redirect()->to('/activity/detail/' . $id)->with('success', 'Aktivitas berhasil ditolak.');
        } else {
            return redirect()->to('/activity/detail/' . $id)->with('error', 'Gagal menolak aktivitas.');
        }
    }

    public function edit($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $activityModel = new ActivityModel();
        $landModel     = new LandModel();
        $userRole      = session()->get('role');
        $userIdKelompok = session()->get('id_kelompok');

        $activity = $activityModel->find($id);

        if (!$activity) {
            return redirect()->to('/activity')->with('error', 'Aktivitas tidak ditemukan.');
        }

        // Only the owner (petani) of a rejected activity can edit it
        if ($userRole === 'petani') {
            if ($activity['id_user'] != session()->get('id_user')) {
                return redirect()->to('/activity')->with('error', 'Anda tidak memiliki akses ke aktivitas ini.');
            }
        }

        if ($activity['status'] !== 'rejected') {
            return redirect()->to('/activity/detail/' . $id)->with('error', 'Hanya aktivitas yang ditolak yang dapat diperbaiki.');
        }

        // Fetch lands for the group
        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $userIdKelompok;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

        $landBuilder = $landModel->builder();
        $landBuilder->select('lands.id_lahan, lands.id_kelompok, lands.nama_lahan, lands.komoditas, lands.status_fase, lands.luas, lands.latitude, lands.longitude, ST_AsGeoJSON(lands.geom, 6, 2) as geojson');
        if ($filterArray) {
            $landBuilder->whereIn('lands.id_kelompok', $filterArray);
        }
        $lands = $landBuilder->orderBy('lands.created_at', 'DESC')->get()->getResultArray();

        // Fetch farmers for the group
        $userModel = new \App\Models\UserModel();
        if ($userRole === 'admin') {
            $farmers = $userModel->where('role', 'petani')->findAll();
        } elseif ($filterArray) {
            $farmers = $userModel->where('role', 'petani')->whereIn('id_kelompok', $filterArray)->findAll();
        }

        // Parse existing deskripsi back into components
        $rawDeskripsi = $activity['deskripsi'] ?? '';
        // Strip rejection note
        $parts = explode('[CATATAN PENOLAKAN]:', $rawDeskripsi, 2);
        $cleanDeskripsi = trim($parts[0]);
        $rejectionNote  = isset($parts[1]) ? trim($parts[1]) : '';

        // Try to parse Dosis/Metode/Catatan from stored string
        $parsedDosis   = '';
        $parsedMetode  = '';
        $parsedCatatan = $cleanDeskripsi;

        if (preg_match('/^Dosis:\s*(.+?)\s*\|\s*Metode:\s*(.+?)\s*\|\s*Catatan:\s*(.*)/s', $cleanDeskripsi, $m)) {
            $parsedDosis   = trim($m[1]);
            $parsedMetode  = trim($m[2]);
            $parsedCatatan = trim($m[3]);
            // Strip any trailing [SISTEM:...] tags from catatan
            $parsedCatatan = preg_replace('/\[SISTEM:.*?\]/s', '', $parsedCatatan);
            $parsedCatatan = trim($parsedCatatan);
        }

        $data = [
            'title'          => 'Perbaiki Aktivitas',
            'nama'           => session()->get('nama'),
            'role'           => $userRole,
            'activity'       => $activity,
            'lands'          => $lands,
            'farmers'        => $farmers,
            'rejectionNote'  => $rejectionNote,
            'parsedDosis'    => $parsedDosis,
            'parsedMetode'   => $parsedMetode,
            'parsedCatatan'  => $parsedCatatan,
        ];

        return view('activity/edit', $data);
    }

    public function update($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $activityModel = new ActivityModel();
        $landModel     = new LandModel();
        $userRole      = session()->get('role');

        $activity = $activityModel->find($id);

        if (!$activity) {
            return redirect()->to('/activity')->with('error', 'Aktivitas tidak ditemukan.');
        }

        if ($activity['status'] !== 'rejected') {
            return redirect()->to('/activity/detail/' . $id)->with('error', 'Hanya aktivitas yang ditolak yang dapat diperbaiki.');
        }

        if ($userRole === 'petani' && $activity['id_user'] != session()->get('id_user')) {
            return redirect()->to('/activity')->with('error', 'Anda tidak memiliki akses.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'id_lahan'        => 'required|integer',
                'jenis_aktivitas' => 'required|min_length[3]|max_length[50]',
                'dosis'           => 'required|min_length[1]|max_length[100]',
                'metode'          => 'required|min_length[1]|max_length[100]',
                'deskripsi'       => 'required|min_length[1]|max_length[500]',
                'foto'            => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]',
                'latitude'        => 'permit_empty|decimal',
                'longitude'       => 'permit_empty|decimal',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $dosis         = $this->request->getPost('dosis');
            $metode        = $this->request->getPost('metode');
            $deskripsiInput = $this->request->getPost('deskripsi');
            $fullDeskripsi = "Dosis: {$dosis} | Metode: {$metode} | Catatan: {$deskripsiInput}";

            $data = [
                'id_lahan'        => $this->request->getPost('id_lahan'),
                'jenis_aktivitas' => $this->request->getPost('jenis_aktivitas'),
                'tanggal'         => date('Y-m-d'),
                'deskripsi'       => $fullDeskripsi,
                'status'          => 'pending',
            ];

            // Simpan hasil panen jika aktivitas panen
            $jenisUpd = strtolower($this->request->getPost('jenis_aktivitas') ?? '');
            if (in_array($jenisUpd, ['panen', 'pemanenan'])) {
                $hasilPanen = $this->request->getPost('hasil_panen');
                if ($hasilPanen !== null && $hasilPanen !== '') {
                    $data['hasil_panen'] = (float)$hasilPanen;
                    $data['satuan']      = $this->request->getPost('satuan') ?? 'Ton';
                }
            }


            // Handle new photo (optional on edit — keep old if none uploaded)
            $foto = $this->request->getFile('foto');
            if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                $newName = $foto->getRandomName();
                $foto->move(WRITEPATH . 'uploads', $newName);
                $data['foto'] = $newName;
            }

            // Handle coordinate
            $latitude  = $this->request->getPost('latitude');
            $longitude = $this->request->getPost('longitude');

            if (!empty($latitude) && !empty($longitude)) {
                $pointGeoJSON = json_encode([
                    'type'        => 'Point',
                    'coordinates' => [(float)$longitude, (float)$latitude],
                ]);

                // Re-run geofencing check
                $geoResult = $landModel->getGeofencingResult($data['id_lahan'], (float)$longitude, (float)$latitude);
                if (!$geoResult || ($geoResult['is_inside'] == 0 && $geoResult['distance'] > 100)) {
                    $data['status'] = 'rejected';
                    $dist = $geoResult ? round($geoResult['distance'], 1) : 'unknown';
                    $data['deskripsi'] .= "\n[SISTEM: Lokasi di luar jangkauan lahan ({$dist}m)]";
                }

                // Update with spatial coordinate
                $db  = \Config\Database::connect();
                $sql = "UPDATE activities SET id_lahan=?, jenis_aktivitas=?, tanggal=?, deskripsi=?, status=?, foto=COALESCE(?, foto), koordinat=ST_GeomFromGeoJSON(?, 2, 4326) WHERE id_aktivitas=?";
                $db->query($sql, [
                    $data['id_lahan'],
                    $data['jenis_aktivitas'],
                    $data['tanggal'],
                    $data['deskripsi'],
                    $data['status'],
                    $data['foto'] ?? null,
                    $pointGeoJSON,
                    $id,
                ]);
            } else {
                $activityModel->update($id, $data);
            }

            // Notify PPL if re-submitted successfully
            if ($data['status'] === 'pending') {
                $notifModel = new \App\Models\NotificationModel();
                $pplUsers   = (new \App\Models\UserModel())
                    ->where('role', 'ppl')
                    ->where('id_kelompok', session()->get('id_kelompok'))
                    ->findAll();
                foreach ($pplUsers as $ppl) {
                    $notifModel->createNotification(
                        $ppl['id_user'],
                        'Aktivitas Diperbaiki & Disubmit Ulang',
                        "Petani " . session()->get('nama') . " telah memperbaiki aktivitas '{$data['jenis_aktivitas']}' dan meminta verifikasi ulang.",
                        'info'
                    );
                }
                return redirect()->to('/activity')->with('success', 'Aktivitas berhasil diperbaiki dan dikirim ulang untuk verifikasi.');
            } else {
                return redirect()->to('/activity/detail/' . $id)->with('error', 'Aktivitas masih ditolak otomatis karena lokasi GPS di luar batas lahan. Pastikan Anda berada di lahan saat mencatat aktivitas.');
            }
        }

        return redirect()->to('/activity/edit/' . $id);
    }

    public function showImage($filename)
    {
        $path = WRITEPATH . 'uploads/' . $filename;
        if (!is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $file = new \CodeIgniter\Files\File($path);
        $mime = $file->getMimeType();
        
        return $this->response
            ->setStatusCode(200)
            ->setContentType($mime)
            ->setBody(file_get_contents($path));
    }
}