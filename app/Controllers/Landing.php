<?php

namespace App\Controllers;

class Landing extends BaseController
{
    public function index()
    {
        // If already logged in, redirect to dashboard or map
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'AgriMapGIS - Solusi Cerdas Pertanian Rajabasa'
        ];

        return view('landing', $data);
    }
}
