<?php

namespace App\Controllers;

use App\Models\FarmerGroupModel;
use App\Models\UserModel;
use App\Models\LandModel;

class FarmerGroup extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $groupModel = new FarmerGroupModel();
        $userModel = new UserModel();
        
        $role = session()->get('role');
        
        $data = [
            'title' => 'Manajemen Kelompok Tani',
            'nama'  => session()->get('nama'),
            'role'  => $role,
        ];

        $db = \Config\Database::connect();

        if ($role === 'petani') {
            $idKelompok = session()->get('id_kelompok');
            if (!$idKelompok) {
                return redirect()->to('/dashboard')->with('error', 'Anda belum terdaftar dalam kelompok tani.');
            }
        } else {
            if (!in_array($role, ['ppl', 'admin'])) {
                return redirect()->to('/login');
            }
            $idKelompok = $this->request->getGet('id');
        }

        if ($idKelompok) {
            // View specific group detail
            $data['group'] = $groupModel->find($idKelompok);
            $members = $userModel->where('id_kelompok', $idKelompok)->where('role', 'petani')->findAll();
            foreach ($members as &$m) {
                $m['total_aktivitas'] = $db->table('activities')->where('id_user', $m['id_user'])->countAllResults();
            }
            $data['members'] = $members;
            
            // Determine if current user is ketua
            $isKetua = false;
            if (session()->get('role') === 'petani' && session()->get('id_kelompok') == $idKelompok) {
                if (stripos($data['group']['ketua'], session()->get('nama')) !== false) {
                    $isKetua = true;
                }
            }
            $data['is_ketua'] = $isKetua;
            
            $landModel = new \App\Models\LandModel();
            $data['land_summary'] = $landModel->getSummaryByKelompok($idKelompok);
            $data['lands'] = $landModel->getLandsByKelompok($idKelompok);
        } else {
            // List all groups - dynamically derive komoditas from actual lands
            $allGroupsRaw = $groupModel->findAll();
            $allGroups = [];
            foreach ($allGroupsRaw as $g) {
                $g['total_anggota'] = $userModel->where('id_kelompok', $g['id_kelompok'])->where('role', 'petani')->countAllResults();
                // Get actual komoditas from lands table (dynamic, not the static field)
                $landKomoditas = $db->table('lands')
                    ->select('komoditas')
                    ->where('id_kelompok', $g['id_kelompok'])
                    ->groupBy('komoditas')
                    ->get()->getResultArray();
                $g['komoditas_aktual'] = implode(', ', array_column($landKomoditas, 'komoditas'));
                // Get land count and total area
                $landStats = $db->table('lands')
                    ->select('COUNT(*) as total_lahan, SUM(luas) as total_luas')
                    ->where('id_kelompok', $g['id_kelompok'])
                    ->get()->getRowArray();
                $g['total_lahan'] = $landStats['total_lahan'] ?? 0;
                $g['total_luas'] = $landStats['total_luas'] ?? 0;
                $allGroups[] = $g;
            }
            $data['all_groups'] = $allGroups;
        }

        return view('farmer_group/index', $data);
    }
    
    public function store()
    {
        if (!in_array(session()->get('role'), ['admin', 'ppl'])) return redirect()->back();
        
        $groupModel = new FarmerGroupModel();
        
        $data = [
            'nama_kelompok' => $this->request->getPost('nama_kelompok'),
            'ketua' => $this->request->getPost('ketua'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'komoditas' => $this->request->getPost('komoditas'),
            'gapoktan' => $this->request->getPost('gapoktan')
        ];
        
        if (session()->get('role') === 'ppl') {
            $data['id_ppl'] = session()->get('id_user');
        }

        $groupModel->insert($data);

        return redirect()->back()->with('success', 'Kelompok Tani berhasil didaftarkan.');
    }
    
    public function update($id)
    {
        if (!in_array(session()->get('role'), ['admin', 'ppl'])) return redirect()->back();
        
        $groupModel = new FarmerGroupModel();
        
        $groupModel->update($id, [
            'nama_kelompok' => $this->request->getPost('nama_kelompok'),
            'ketua' => $this->request->getPost('ketua'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'komoditas' => $this->request->getPost('komoditas'),
            'gapoktan' => $this->request->getPost('gapoktan')
        ]);

        return redirect()->back()->with('success', 'Data Kelompok Tani berhasil diperbarui.');
    }
    
    public function storeFarmer()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');
        
        $userModel = new UserModel();
        
        if (session()->get('role') === 'petani') {
            $idKelompok = session()->get('id_kelompok');
            
            // Verify if user is ketua
            $groupModel = new FarmerGroupModel();
            $group = $groupModel->find($idKelompok);
            if (!$group || stripos($group['ketua'], session()->get('nama')) === false) {
                return redirect()->back()->with('error', 'Hanya Ketua Kelompok yang dapat mengelola anggota.');
            }
        } else {
            if (!in_array(session()->get('role'), ['admin', 'ppl'])) return redirect()->back();
            $idKelompok = $this->request->getPost('id_kelompok');
        }
        
        $email = $this->request->getPost('email');
        
        // Cek apakah email sudah ada
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Gagal: Email sudah digunakan. Silakan gunakan email lain.');
        }
        
        try {
            $userModel->insert([
                'nama' => $this->request->getPost('nama'),
                'email' => $email,
                'password' => $this->request->getPost('password'), // No need to hash here, UserModel handles it
                'telepon' => $this->request->getPost('telepon'),
                'role' => 'petani',
                'id_kelompok' => $idKelompok
            ]);
            
            return redirect()->back()->with('success', 'Anggota Petani berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data petani.');
        }
    }

    public function updateFarmer($id)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');
        
        $userModel = new UserModel();
        $farmer = $userModel->find($id);
        if (!$farmer || $farmer['role'] !== 'petani') {
            return redirect()->back()->with('error', 'Data anggota tidak ditemukan.');
        }

        if (session()->get('role') === 'petani') {
            $idKelompok = session()->get('id_kelompok');
            if ($farmer['id_kelompok'] != $idKelompok) return redirect()->back()->with('error', 'Akses ditolak.');
            
            $groupModel = new FarmerGroupModel();
            $group = $groupModel->find($idKelompok);
            if (!$group || stripos($group['ketua'], session()->get('nama')) === false) {
                return redirect()->back()->with('error', 'Hanya Ketua Kelompok yang dapat mengelola anggota.');
            }
        } elseif (!in_array(session()->get('role'), ['admin', 'ppl'])) {
            return redirect()->back();
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon')
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        $userModel->update($id, $data);
        return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function deleteFarmer($id)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');
        
        $userModel = new UserModel();
        $farmer = $userModel->find($id);
        if (!$farmer || $farmer['role'] !== 'petani') {
            return redirect()->back()->with('error', 'Data anggota tidak ditemukan.');
        }

        if (session()->get('role') === 'petani') {
            $idKelompok = session()->get('id_kelompok');
            if ($farmer['id_kelompok'] != $idKelompok) return redirect()->back()->with('error', 'Akses ditolak.');
            
            $groupModel = new FarmerGroupModel();
            $group = $groupModel->find($idKelompok);
            if (!$group || stripos($group['ketua'], session()->get('nama')) === false) {
                return redirect()->back()->with('error', 'Hanya Ketua Kelompok yang dapat mengelola anggota.');
            }
        } elseif (!in_array(session()->get('role'), ['admin', 'ppl'])) {
            return redirect()->back();
        }

        $userModel->delete($id);
        return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
    }

    public function storeLand()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');
        
        $landModel = new \App\Models\LandModel();
        $idKelompok = $this->request->getPost('id_kelompok');
        $idUser     = $this->request->getPost('id_user');

        // Only admin and ppl can add land
        if (session()->get('role') === 'petani') {
            return redirect()->back()->with('error', 'Gagal: Anda tidak memiliki akses untuk menambah lahan. Silakan hubungi PPL Anda.');
        }

        // Validate geojson is provided (polygon must be drawn or location used)
        $geojson = $this->request->getPost('geojson');
        if (empty($geojson)) {
            return redirect()->back()->with('error', 'Gagal: Data spasial lahan belum ada. Silakan gambar poligon lahan di peta atau gunakan tombol "Gunakan Lokasi Saat Ini".');
        }

        $luasInput = (float)$this->request->getPost('luas');

        // Validate polygon area vs. manually entered area (tolerance 50%)
        if (!empty($geojson) && $luasInput > 0) {
            try {
                $db = \Config\Database::connect();
                // For SRID 4326 in MySQL 8, ST_Area returns square meters natively, so just divide by 10000 to get Ha
                $areaSql = "SELECT ST_Area(ST_GeomFromGeoJSON(?)) / 10000 as area";
                $areaRes = $db->query($areaSql, [$geojson])->getRowArray();
                $luasDiGambar = (float)($areaRes['area'] ?? 0);
                
                // If luasDiGambar is extremely small (< 0.01), it means the DB is returning square degrees (MariaDB)
                // In this case, we bypass the validation because it's not in square meters.
                if ($luasDiGambar > 0.01) {
                    $selisihPersen = abs($luasInput - $luasDiGambar) / $luasDiGambar * 100;
                    if ($selisihPersen > 50) {
                        return redirect()->back()->with('error', 
                            sprintf(
                                'Luas yang diinput (%.2f Ha) berbeda terlalu jauh dari luas poligon di peta (%.2f Ha, selisih %.0f%%). Harap periksa kembali.',
                                $luasInput, $luasDiGambar, $selisihPersen
                            )
                        );
                    }
                }
            } catch (\Exception $e) {
                // If ST_Area fails, just skip the validation (e.g. non-spatial DB)
            }
        }

        $data = [
            'id_kelompok' => $idKelompok,
            'id_user'     => $idUser,
            'nama_lahan'  => $this->request->getPost('nama_lahan'),
            'komoditas'   => $this->request->getPost('komoditas'),
            'alamat'      => $this->request->getPost('alamat'),
            'luas'        => $luasInput,
            'latitude'    => $this->request->getPost('lat') ?? $this->request->getPost('latitude'),
            'longitude'   => $this->request->getPost('lng') ?? $this->request->getPost('longitude'),
            'status_fase' => 'persiapan'
        ];

        try {
            $landModel->insertLandWithGeoJSON($data, $geojson);
            return redirect()->to('/peta-gis')->with('success', 'Lahan baru berhasil ditambahkan ke kelompok!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan lahan: ' . $e->getMessage());
        }
    }

    public function updateLand()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }

        if (session()->get('role') === 'petani') {
            return redirect()->back()->with('error', 'Gagal: Anda tidak memiliki akses untuk mengedit lahan.');
        }

        $idLahan    = (int)$this->request->getPost('id_lahan');
        $idKelompok = $this->request->getPost('id_kelompok');
        $idUser     = $this->request->getPost('id_user');
        $geojson    = $this->request->getPost('geojson');
        $luasInput  = (float)$this->request->getPost('luas');

        if (!$idLahan) {
            return redirect()->back()->with('error', 'Gagal: ID Lahan tidak ditemukan.');
        }

        if (empty($geojson)) {
            return redirect()->back()->with('error', 'Gagal: Data poligon lahan belum ada. Pastikan poligon sudah digambar di peta.');
        }

        // Validate polygon area vs. manually entered area (tolerance 50%)
        if ($luasInput > 0) {
            try {
                $db = \Config\Database::connect();
                $areaSql = "SELECT ST_Area(ST_GeomFromGeoJSON(?)) / 10000 as area";
                $areaRes = $db->query($areaSql, [$geojson])->getRowArray();
                $luasDiGambar = (float)($areaRes['area'] ?? 0);

                // If luasDiGambar is extremely small (< 0.01), bypass validation because MariaDB returns square degrees for SRID 4326
                if ($luasDiGambar > 0.01) {
                    $selisihPersen = abs($luasInput - $luasDiGambar) / $luasDiGambar * 100;
                    if ($selisihPersen > 50) {
                        return redirect()->back()->with('error',
                            sprintf(
                                'Luas yang diinput (%.2f Ha) berbeda terlalu jauh dari luas poligon di peta (%.2f Ha, selisih %.0f%%). Harap periksa kembali.',
                                $luasInput, $luasDiGambar, $selisihPersen
                            )
                        );
                    }
                }
            } catch (\Exception $e) {
                // Skip validation if ST_Area fails
            }
        }

        $landModel    = new \App\Models\LandModel();
        $existingLand = $landModel->find($idLahan);
        if (!$existingLand) {
            return redirect()->back()->with('error', 'Gagal: Lahan tidak ditemukan di database.');
        }

        $data = [
            'id_kelompok' => $idKelompok,
            'id_user'     => $idUser ?: $existingLand['id_user'],
            'nama_lahan'  => $this->request->getPost('nama_lahan'),
            'komoditas'   => $this->request->getPost('komoditas'),
            'alamat'      => $this->request->getPost('alamat') ?? $existingLand['alamat'],
            'luas'        => $luasInput,
            'latitude'    => $this->request->getPost('lat') ?? $this->request->getPost('latitude') ?? $existingLand['latitude'],
            'longitude'   => $this->request->getPost('lng') ?? $this->request->getPost('longitude') ?? $existingLand['longitude'],
            'status_fase' => $existingLand['status_fase'],
        ];

        try {
            $landModel->updateLandWithGeoJSON($idLahan, $data, $geojson);
            return redirect()->to('/peta-gis')->with('success', 'Data lahan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui lahan: ' . $e->getMessage());
        }
    }


    public function deleteLand($id)
    {
        $landModel = new LandModel();
        $land = $landModel->find($id);

        if (!$land) {
            return redirect()->back()->with('error', 'Lahan tidak ditemukan.');
        }

        // Security check: PPL can only delete lands in their group OR lands they added
        $userRole = session()->get('role');
        $idKelompok = session()->get('id_kelompok');
        $idUser = session()->get('id_user');

        if ($userRole !== 'admin' && $land['id_kelompok'] != $idKelompok && $land['id_user'] != $idUser) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menghapus lahan ini.');
        }

        if ($landModel->delete($id)) {
            return redirect()->back()->with('success', 'Lahan berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus lahan.');
        }
    }

    public function getFarmersByGroup($id)
    {
        $userModel = new UserModel();
        $farmers = $userModel->where('id_kelompok', $id)->where('role', 'petani')->findAll();
        return $this->response->setJSON($farmers);
    }
}
