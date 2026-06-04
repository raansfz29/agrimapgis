<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\FarmerGroupModel;

class Profile extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $groupModel = new FarmerGroupModel();
        
        $userId = session()->get('id_user');
        $user = $userModel->find($userId);
        $group = $groupModel->find($user['id_kelompok']);

        $data = [
            'title' => 'Profil Saya',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'user'  => $user,
            'group' => $group
        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $userId = session()->get('id_user');
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
        ];

        // Only update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        if ($userModel->update($userId, $data)) {
            session()->set('nama', $data['nama']); // Update session name
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
        } else {
            $errorMsg = 'Gagal memperbarui profil.';
            if ($userModel->errors()) {
                $errorMsg .= ' ' . implode(', ', $userModel->errors());
            }
            return redirect()->back()->with('error', $errorMsg);
        }
    }
}
