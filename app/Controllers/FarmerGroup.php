<?php

namespace App\Controllers;

use App\Models\FarmerGroupModel;
use App\Models\UserModel;
use App\Models\LandModel;

class FarmerGroup extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in') || !in_array(session()->get('role'), ['ppl', 'admin'])) {
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

        $idKelompok = $this->request->getGet('id');

        if ($idKelompok) {
            // View specific group detail
            $data['group'] = $groupModel->find($idKelompok);
            $members = $userModel->where('id_kelompok', $idKelompok)->where('role', 'petani')->findAll();
            foreach ($members as &$m) {
                $m['total_aktivitas'] = $db->table('activities')->where('id_user', $m['id_user'])->countAllResults();
            }
            $data['members'] = $members;
            
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
        
        $groupModel->insert([
            'nama_kelompok' => $this->request->getPost('nama_kelompok'),
            'ketua' => $this->request->getPost('ketua'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'komoditas' => $this->request->getPost('komoditas'),
            'gapoktan' => $this->request->getPost('gapoktan')
        ]);

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
        if (!in_array(session()->get('role'), ['admin', 'ppl'])) return redirect()->back();
        
        $userModel = new UserModel();
        $idKelompok = $this->request->getPost('id_kelompok');
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

    public function storeLand()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');
        
        $landModel = new \App\Models\LandModel();
        $idKelompok = $this->request->getPost('id_kelompok');
        $idUser = $this->request->getPost('id_user');

        // If user is a farmer, force their own ID and group
        if (session()->get('role') === 'petani') {
            $idUser = session()->get('id_user');
            $idKelompok = session()->get('id_kelompok');
        }

        $geojson = $this->request->getPost('geojson');
        if (empty($geojson)) {
            return redirect()->back()->with('error', 'Gagal: Silakan klik tombol "Gunakan Lokasi Saat Ini" atau isi koordinat untuk menentukan titik lahan.');
        }

        $data = [
            'id_kelompok' => $idKelompok,
            'id_user'     => $idUser,
            'nama_lahan'  => $this->request->getPost('nama_lahan'),
            'komoditas'   => $this->request->getPost('komoditas'),
            'alamat'      => $this->request->getPost('alamat'),
            'luas'        => $this->request->getPost('luas'),
            'latitude'    => $this->request->getPost('latitude'),
            'longitude'   => $this->request->getPost('longitude'),
            'status_fase' => 'persiapan'
        ];

        try {
            $landModel->insertLandWithGeoJSON($data, $this->request->getPost('geojson'));
            return redirect()->back()->with('success', 'Lahan baru berhasil ditambahkan ke kelompok ini.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan lahan: ' . $e->getMessage());
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
