<?php

namespace App\Controllers;

use App\Models\LandModel;
use App\Models\ActivityModel;
use App\Models\FarmerGroupModel;
use App\Models\UserModel;

class Trace extends BaseController
{
    /**
     * Public traceability page — no login required.
     * Accessed via QR code scan: /trace/{id_lahan}
     */
    public function index($id)
    {
        $landModel    = new LandModel();
        $activityModel = new ActivityModel();
        $groupModel   = new FarmerGroupModel();
        $userModel    = new UserModel();

        $land = $landModel->find($id);
        if (!$land) {
            return redirect()->to('/')->with('error', 'Lahan tidak ditemukan.');
        }

        // Get approved activities (activity history)
        $activities = $activityModel->builder()
            ->select('activities.*, users.nama as nama_petani')
            ->join('users', 'users.id_user = activities.id_user', 'left')
            ->where('activities.id_lahan', $id)
            ->where('activities.status', 'approved')
            ->orderBy('activities.tanggal', 'ASC')
            ->get()->getResultArray();

        // Group info
        $group  = $groupModel->find($land['id_kelompok']);
        $farmer = $userModel->find($land['id_user']);

        // Summary stats
        $totalPanen = 0;
        $panenCount = 0;
        foreach ($activities as $act) {
            if ($act['jenis_aktivitas'] === 'panen' && $act['hasil_panen'] > 0) {
                $totalPanen += $act['hasil_panen'];
                $panenCount++;
            }
        }

        $data = [
            'title'      => 'Riwayat Lahan: ' . ($land['nama_lahan'] ?? ''),
            'land'       => $land,
            'activities' => $activities,
            'group'      => $group,
            'farmer'     => $farmer,
            'totalPanen' => $totalPanen,
            'panenCount' => $panenCount,
            'traceUrl'   => current_url(),
        ];

        return view('trace/index', $data);
    }
}
