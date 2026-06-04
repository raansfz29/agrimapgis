<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\FarmerGroupModel;
use App\Models\UserModel;
use App\Models\LandModel;
use App\Models\ActivityModel;

class AgriSeeder extends Seeder
{
    public function run()
    {
        // Truncate tables to avoid duplicates
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('activities')->truncate();
        $db->table('lands')->truncate();
        $db->table('users')->truncate();
        $db->table('farmer_groups')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 1. Insert Farmer Group
        $groupModel = new FarmerGroupModel();
        $groupId = $groupModel->insert([
            'nama_kelompok' => 'Kelompok Tani Maju Jaya',
            'ketua' => 'Budi Santoso',
            'kecamatan' => 'Rajabasa'
        ]);

        // 2. Insert Users
        $userModel = new UserModel();
        
        // PPL (Petugas Penyuluh Lapangan)
        $userModel->insert([
            'nama' => 'Petugas PPL Rajabasa',
            'email' => 'ppl@agrimapgis.test',
            'password' => 'ppl123',
            'role' => 'ppl',
            'id_kelompok' => $groupId,
            'telepon' => '081234567890'
        ]);

        // Petani
        $userModel->insert([
            'nama' => 'Budi Santoso',
            'email' => 'budi@agrimapgis.test',
            'password' => 'petani123',
            'role' => 'petani',
            'id_kelompok' => $groupId,
            'telepon' => '081298765432'
        ]);

        // 3. Insert Dummy Lands (Sawah)
        $landModel = new LandModel();

        // Polygon Padi
        $polygonPadi = '{
            "type": "Polygon",
            "coordinates": [
                [
                    [105.2589, -5.3855],
                    [105.2600, -5.3855],
                    [105.2600, -5.3845],
                    [105.2589, -5.3845],
                    [105.2589, -5.3855]
                ]
            ]
        }';

        $landId1 = $landModel->insertLandWithGeoJSON([
            'id_kelompok' => $groupId,
            'nama_lahan' => 'Sawah Blok A',
            'komoditas' => 'padi',
            'status_fase' => 'tanam'
        ], $polygonPadi);

        // Polygon Jagung
        $polygonJagung = '{
            "type": "Polygon",
            "coordinates": [
                [
                    [105.2610, -5.3850],
                    [105.2625, -5.3850],
                    [105.2625, -5.3840],
                    [105.2610, -5.3840],
                    [105.2610, -5.3850]
                ]
            ]
        }';

        $landId2 = $landModel->insertLandWithGeoJSON([
            'id_kelompok' => $groupId,
            'nama_lahan' => 'Ladang Jagung B',
            'komoditas' => 'jagung',
            'status_fase' => 'persiapan'
        ], $polygonJagung);

        // 4. Insert Sample Activities
        $activityModel = new ActivityModel();

        $activityModel->insert([
            'id_lahan' => $landId1,
            'id_user' => $userModel->where('email', 'budi@agrimapgis.test')->first()['id_user'],
            'jenis_aktivitas' => 'Penanaman',
            'tanggal' => date('Y-m-d'),
            'deskripsi' => 'Penanaman bibit padi varietas Ciherang di blok A',
            'status' => 'approved'
        ]);

        $activityModel->insert([
            'id_lahan' => $landId2,
            'id_user' => $userModel->where('email', 'budi@agrimapgis.test')->first()['id_user'],
            'jenis_aktivitas' => 'Pengolahan Tanah',
            'tanggal' => date('Y-m-d', strtotime('-1 day')),
            'deskripsi' => 'Pengolahan tanah untuk persiapan tanam jagung',
            'status' => 'pending'
        ]);

        echo "Seeder AgriSeeder berhasil dijalankan!\n";
    }
}