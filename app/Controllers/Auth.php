<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - AgriMapGIS'
        ];
        return view('auth/login', $data);
    }

    public function loginSubmit()
    {
        $session = session();
        $userModel = new UserModel();
        $groupModel = new \App\Models\FarmerGroupModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                if (!empty($user['id_kelompok'])) {
                    $group = $groupModel->find($user['id_kelompok']);
                    $namaKelompok = $group ? $group['nama_kelompok'] : '-';
                } else {
                    $namaKelompok = '-';
                }

                $sessionData = [
                    'id_user'      => $user['id_user'],
                    'nama'         => $user['nama'],
                    'email'        => $user['email'],
                    'role'         => $user['role'],
                    'id_kelompok'  => $user['id_kelompok'],
                    'nama_kelompok'=> $namaKelompok,
                    'is_logged_in' => true
                ];
                $session->set($sessionData);
                return redirect()->to('/dashboard')->with('success', 'Selamat datang kembali, ' . $user['nama']);
            } else {
                $session->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->back();
            }
        } else {
            $session->setFlashdata('error', 'Email tidak terdaftar.');
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function register()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        $groupModel = new \App\Models\FarmerGroupModel();
        $groups = $groupModel->findAll();

        $data = [
            'title' => 'Daftar Akun - AgriMapGIS',
            'groups' => $groups
        ];
        return view('auth/register', $data);
    }

    public function registerSubmit()
    {
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'ppl', // Default role for registration is PPL
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($userModel->insert($data)) {
            return redirect()->to('/login')->with('success', 'Akun berhasil didaftarkan. Silakan login.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan akun.');
        }
    }
}