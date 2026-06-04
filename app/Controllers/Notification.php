<?php

namespace App\Controllers;

class Notification extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $notifModel = new \App\Models\NotificationModel();
        $userId     = session()->get('id_user');

        $raw = $notifModel
            ->where('id_user', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Map DB columns → view-friendly fields
        $notifications = array_map(function ($n) {
            $tipe = $n['tipe'] ?? 'info';

            // Icon & colour map
            $iconMap  = [
                'danger'  => 'fas fa-exclamation-triangle',
                'warning' => 'fas fa-exclamation-circle',
                'success' => 'fas fa-check-circle',
                'info'    => 'fas fa-info-circle',
            ];
            $colorMap = [
                'danger'  => 'danger',
                'warning' => 'warning',
                'success' => 'success',
                'info'    => 'info',
            ];

            // Derive category from tipe
            $categoryMap = [
                'danger'  => 'peringatan',
                'warning' => 'peringatan',
                'success' => 'laporan',
                'info'    => 'laporan',
            ];
            $cat = $categoryMap[$tipe] ?? 'sistem';
            
            // Further refine info category based on title
            if ($tipe === 'info') {
                if (stripos($n['judul'], 'pesan baru') !== false) {
                    $cat = 'sistem';
                }
            }

            // Human-readable relative time
            $created = strtotime($n['created_at']);
            $diff    = time() - $created;
            if ($diff < 60)            $time = 'Baru saja';
            elseif ($diff < 3600)      $time = round($diff / 60) . ' menit lalu';
            elseif ($diff < 86400)     $time = round($diff / 3600) . ' jam lalu';
            elseif ($diff < 604800)    $time = round($diff / 86400) . ' hari lalu';
            else                       $time = date('d M Y', $created);

            return [
                'id'       => $n['id_notif'],
                'title'    => $n['judul'],
                'message'  => $n['pesan'],
                'tipe'     => $tipe,
                'icon'     => $iconMap[$tipe]  ?? 'fas fa-bell',
                'color'    => $colorMap[$tipe] ?? 'info',
                'category' => $cat,
                'is_read'  => (bool)$n['is_read'],
                'time'     => $time,
                'created_at' => $n['created_at'],
            ];
        }, $raw);

        $unreadCount = count(array_filter($notifications, fn($n) => !$n['is_read']));

        $data = [
            'title'        => 'Notifikasi',
            'nama'         => session()->get('nama'),
            'role'         => session()->get('role'),
            'notifications' => $notifications,
            'unreadCount'  => $unreadCount,
        ];

        return view('notification/index', $data);
    }

    public function apiGet()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setStatusCode(401);
        }

        $notifModel = new \App\Models\NotificationModel();
        $userId     = session()->get('id_user');
        $unread     = $notifModel->getUnreadByUser($userId);

        return $this->response->setJSON([
            'unread_count'  => count($unread),
            'notifications' => $unread,
        ]);
    }

    public function markRead($id)
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error']);
        }

        $notifModel = new \App\Models\NotificationModel();
        $notifModel->update($id, ['is_read' => 1]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function markAllRead()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error']);
        }

        $notifModel = new \App\Models\NotificationModel();
        $userId     = session()->get('id_user');

        $db = \Config\Database::connect();
        $db->query("UPDATE notifications SET is_read = 1 WHERE id_user = ?", [$userId]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function clearAll()
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error']);
        }

        $userId = session()->get('id_user');
        $db     = \Config\Database::connect();
        $db->query("DELETE FROM notifications WHERE id_user = ? AND is_read = 1", [$userId]);

        return $this->response->setJSON(['status' => 'success']);
    }
}
