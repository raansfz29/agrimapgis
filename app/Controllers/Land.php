<?php

namespace App\Controllers;

use App\Models\LandModel;
use App\Models\FarmerGroupModel;

class Land extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $userRole = session()->get('role');
        $idKelompok = session()->get('id_kelompok');

        // Get lands based on role
        if ($userRole === 'admin') {
            $lands = $landModel->getLandsByKelompok(null); // Show all
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            if (empty($managedGroupIds)) $managedGroupIds = [0];

            $lands = $landModel->builder()
                ->whereIn('id_kelompok', $managedGroupIds)
                ->orderBy('created_at', 'DESC')
                ->get()->getResultArray();
        } else {
            // Show group lands + anything they added personally
            $lands = $landModel->builder()
                ->groupStart()
                    ->where('id_kelompok', $idKelompok)
                    ->orWhere('id_user', session()->get('id_user'))
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Kelola Lahan Pertanian',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'lands' => $lands
        ];

        return view('land/index', $data);
    }

    public function detail($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $land = $landModel->getLandsGeoJSON($id);

        if (!$land) {
            return redirect()->to('/land')->with('error', 'Lahan tidak ditemukan.');
        }

        // Check permission: Admin can see all. Others see their group OR what they added.
        $userRole = session()->get('role');
        $idKelompok = session()->get('id_kelompok');
        $idUser = session()->get('id_user');

        if ($userRole === 'ppl') {
            $groupModel = new FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', $idUser)->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            if (!in_array($land['id_kelompok'], $managedGroupIds)) {
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak membawahi kelompok tani untuk lahan ini.');
            }
        } elseif ($userRole !== 'admin' && $land['id_kelompok'] != $idKelompok && $land['id_user'] != $idUser) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki hak untuk melihat detail lahan ini.');
        }

        // Get group info for additional details
        $groupModel = new FarmerGroupModel();
        $group = $groupModel->find($land['id_kelompok']);
        $land['nama_kelompok'] = $group ? $group['nama_kelompok'] : 'Tidak ada kelompok';
        $land['kecamatan'] = $group ? $group['kecamatan'] : 'Tidak diketahui';

        // Format coordinates
        if (!empty($land['latitude']) && !empty($land['longitude'])) {
            $land['koordinat_tengah'] = round($land['latitude'], 6) . ', ' . round($land['longitude'], 6);
        } else {
            $land['koordinat_tengah'] = 'Tidak tersedia';
        }

        $data = [
            'title' => 'Detail Lahan: ' . $land['nama_lahan'],
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'land'  => $land,
            'activities' => (new \App\Models\ActivityModel())->where('id_lahan', $id)->orderBy('tanggal', 'DESC')->findAll()
        ];

        return view('land/detail', $data);
    }

    public function updateDetail($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role === 'petani') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit detail lahan.');
        }

        $nama   = $this->request->getPost('nama_lahan');
        $komo   = $this->request->getPost('komoditas');
        $alamat = $this->request->getPost('alamat');
        $luas   = $this->request->getPost('luas');

        if (empty($nama) || empty($komo)) {
            return redirect()->back()->with('error', 'Nama lahan dan komoditas wajib diisi.');
        }

        // Use raw query to bypass any CI4 ORM primary key resolution issue
        $db = \Config\Database::connect();
        
        $setClauses = ['nama_lahan = ?', 'komoditas = ?', 'alamat = ?'];
        $params     = [$nama, $komo, $alamat];
        
        if (!empty($luas)) {
            // Replace comma with dot to support Indonesian decimal format
            $luas = str_replace(',', '.', $luas);
            if ((float)$luas > 0) {
                $setClauses[] = 'luas = ?';
                $params[]     = (float)$luas;
            }
        }
        
        $params[] = (int)$id; // WHERE id_lahan = ?
        
        $sql = "UPDATE lands SET " . implode(', ', $setClauses) . " WHERE id_lahan = ?";
        
        $res      = $db->query($sql, $params);
        $affected = $db->affectedRows();
        
        if ($res) {
            return redirect()->to('/land/detail/' . $id)->with('success', 'Detail lahan berhasil diperbarui!');
        } else {
            $err = $db->error();
            return redirect()->back()->with('error', 'Gagal memperbarui: ' . ($err['message'] ?? 'Unknown error'));
        }
    }
}
