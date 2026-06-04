<?php

namespace App\Controllers;

use App\Models\LandModel;

class Map extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $groupModel = new \App\Models\FarmerGroupModel();
        $data = [
            'title' => 'Peta Utama - AgriMapGIS',
            'role'  => session()->get('role'),
            'groups' => $groupModel->findAll()
        ];
        
        return view('map/index', $data);
    }

    public function thematic()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Peta Tematik - AgriMapGIS'
        ];
        
        return view('map/thematic', $data);
    }

    public function saveLand()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->back();
        }

        $payload = $this->request->getJSON(true);
        if (!$payload) {
            $payload = $this->request->getPost();
        }

        $rules = [
            'nama_lahan' => 'required|min_length[3]|max_length[100]',
            'komoditas' => 'required|in_list[padi,jagung]',
            'geojson' => 'required',
        ];

        if (!$this->validateData($payload, $rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => implode('\n', $this->validator->getErrors())]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idKelompok = session()->get('id_kelompok');
        if (!$idKelompok) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu.']);
            }
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $data = [
            'id_kelompok' => $idKelompok,
            'id_user'     => session()->get('id_user'),  // FIX: link land to user
            'nama_lahan'  => $payload['nama_lahan'],
            'komoditas'   => $payload['komoditas'],
            'status_fase' => $payload['status_fase'] ?? 'persiapan'
        ];

        try {
            $insertedId = $landModel->insertLandWithGeoJSON($data, $payload['geojson']);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'id' => $insertedId]);
            }
            return redirect()->to('/peta-gis')->with('success', 'Lahan berhasil disimpan dengan ID ' . $insertedId);
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan lahan: ' . $e->getMessage());
        }
    }

    public function apiLands()
    {
        $userRole = session()->get('role');
        $userId = session()->get('id_user');
        $landModel = new LandModel();
        
        $builder = $landModel->builder();
        $builder->select('id_lahan, id_kelompok, nama_lahan, komoditas, status_fase, status_bencana, alamat, luas, latitude, longitude, created_at');
        $builder->select('ST_AsGeoJSON(geom, 6, 2) as geojson');
        
        $reqGroup = $this->request->getGet('id_kelompok');

        if ($userRole === 'admin') {
            // Admin sees everything unless filtered
            if ($reqGroup) {
                $builder->where('id_kelompok', $reqGroup);
            }
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            if (empty($managedGroupIds)) $managedGroupIds = [0];
            
            if ($reqGroup && in_array($reqGroup, $managedGroupIds)) {
                $builder->where('id_kelompok', $reqGroup);
            } else {
                $builder->whereIn('id_kelompok', $managedGroupIds);
            }
        } else {
            // For Petani
            $builder->groupStart()
                    ->where('id_kelompok', session()->get('id_kelompok'))
                    ->orWhere('id_user', $userId)
                    ->groupEnd();
        }
        
        $lands = $builder->get()->getResultArray();

        $features = [];
        foreach ($lands as $l) {
            if (!empty($l['geojson'])) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => json_decode($l['geojson'], true),
                    'properties' => [
                        'id_lahan' => $l['id_lahan'],
                        'nama_lahan' => $l['nama_lahan'],
                        'komoditas' => $l['komoditas'],
                        'status_fase' => $l['status_fase'],
                        'status_bencana' => $l['status_bencana'],
                        'luas' => $l['luas'],
                        'latitude' => $l['latitude'],
                        'longitude' => $l['longitude'],
                        'estimasi_panen' => $landModel->getHarvestPrediction($l['id_lahan'])
                    ]
                ];
            }
        }

        return $this->response->setJSON([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }
}