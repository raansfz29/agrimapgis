<?php

namespace App\Controllers;

use App\Models\MessageModel;
use App\Models\UserModel;
use App\Models\NotificationModel;

class Message extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $userRole = session()->get('role');
        $idKelompok = session()->get('id_kelompok');

        $db = \Config\Database::connect();
        // Get list of contacts - include kelompok tani name
        if ($userRole === 'ppl' || $userRole === 'admin') {
            $contacts = $db->table('users')
                ->select('users.*, farmer_groups.nama_kelompok')
                ->join('farmer_groups', 'farmer_groups.id_kelompok = users.id_kelompok', 'left')
                ->where('users.role', 'petani')
                ->get()->getResultArray();
        } else {
            // For petani: show PPL and other members in the same group
            $contacts = $db->table('users')
                ->select('users.*, farmer_groups.nama_kelompok')
                ->join('farmer_groups', 'farmer_groups.id_kelompok = users.id_kelompok', 'left')
                ->groupStart()
                    ->where('users.role', 'ppl')
                    ->orGroupStart()
                        ->where('users.role', 'petani')
                        ->where('users.id_kelompok', $idKelompok)
                        ->where('users.id_user !=', session()->get('id_user'))
                    ->groupEnd()
                ->groupEnd()
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Pesan Internal',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'contacts' => $contacts
        ];

        return view('message/index', $data);
    }

    public function chat($targetId)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $messageModel = new MessageModel();
        $userModel = new UserModel();
        $userId = session()->get('id_user');

        $targetUser = $userModel->find($targetId);
        if (!$targetUser) return redirect()->to('/message');

        $messages = $messageModel->getConversation($userId, $targetId);

        // Mark as read
        $messageModel->where('id_pengirim', $targetId)->where('id_penerima', $userId)->set(['is_read' => 1])->update();

        $data = [
            'title' => 'Chat dengan ' . $targetUser['nama'],
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'target' => $targetUser,
            'messages' => $messages
        ];

        return view('message/chat', $data);
    }

    public function send()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $messageModel = new MessageModel();
        $data = [
            'id_pengirim' => session()->get('id_user'),
            'id_penerima' => $this->request->getPost('id_penerima'),
            'isi_pesan' => $this->request->getPost('isi_pesan'),
            'is_read' => 0
        ];

        $messageModel->insert($data);

        // Create Notification for Receiver
        $notifModel = new NotificationModel();
        $senderName = session()->get('nama');
        $notifModel->createNotification(
            $data['id_penerima'],
            'Pesan Baru dari ' . $senderName,
            substr($data['isi_pesan'], 0, 50) . (strlen($data['isi_pesan']) > 50 ? '...' : ''),
            'info'
        );

        return redirect()->to('/message/chat/' . $data['id_penerima']);
    }

    // Returns messages as JSON for inline AJAX chat
    public function messages($targetId)
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setJSON([]);
        }

        $messageModel = new MessageModel();
        $userId = session()->get('id_user');

        // Mark as read
        $messageModel->where('id_pengirim', $targetId)->where('id_penerima', $userId)->set(['is_read' => 1])->update();

        $messages = $messageModel->getConversation($userId, $targetId);
        return $this->response->setJSON($messages);
    }

    // Send message via AJAX (no redirect)
    public function sendAjax()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $messageModel = new MessageModel();
        $notifModel = new NotificationModel();

        $data = [
            'id_pengirim' => session()->get('id_user'),
            'id_penerima' => $this->request->getPost('id_penerima'),
            'isi_pesan'   => $this->request->getPost('isi_pesan'),
            'is_read'     => 0
        ];

        $messageModel->insert($data);

        $senderName = session()->get('nama');
        $notifModel->createNotification(
            $data['id_penerima'],
            'Pesan Baru dari ' . $senderName,
            substr($data['isi_pesan'], 0, 50) . (strlen($data['isi_pesan']) > 50 ? '...' : ''),
            'info'
        );

        return $this->response
            ->setHeader('X-CSRF-TOKEN', csrf_hash())
            ->setJSON(['status' => 'ok']);
    }
}
