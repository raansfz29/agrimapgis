<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id_notif';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_user', 'judul', 'pesan', 'tipe', 'is_read', 'created_at'];

    /**
     * Get all unread notifications for a user, newest first.
     */
    public function getUnreadByUser($userId)
    {
        return $this->where('id_user', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Create a single notification for one user.
     *
     * @param int    $userId
     * @param string $title
     * @param string $message
     * @param string $type    'info' | 'success' | 'warning' | 'danger'
     */
    public function createNotification(int $userId, string $title, string $message, string $type = 'info'): bool|int
    {
        return $this->insert([
            'id_user' => $userId,
            'judul'   => $title,
            'pesan'   => $message,
            'tipe'    => $type,
            'is_read' => 0,
        ]);
    }

    /**
     * Broadcast a notification to all users with a given role inside a kelompok.
     *
     * @param int    $idKelompok
     * @param string $role       'ppl' | 'petani' | 'admin'
     * @param string $title
     * @param string $message
     * @param string $type
     */
    public function broadcastToGroup(int $idKelompok, string $role, string $title, string $message, string $type = 'info'): void
    {
        $userModel = new UserModel();
        $users = [];

        if ($role === 'ppl') {
            $groupModel = new FarmerGroupModel();
            $group = $groupModel->find($idKelompok);
            if ($group && !empty($group['id_ppl'])) {
                $user = $userModel->find($group['id_ppl']);
                if ($user) $users[] = $user;
            }
        } else {
            $builder = $userModel->builder();
            if ($role !== 'admin') {
                $builder->where('id_kelompok', $idKelompok);
            }
            $builder->where('role', $role);
            $users = $builder->get()->getResultArray();
        }

        foreach ($users as $user) {
            $this->createNotification((int)$user['id_user'], $title, $message, $type);
        }
    }

    /**
     * Count unread notifications for a user.
     */
    public function countUnread(int $userId): int
    {
        return $this->where('id_user', $userId)->where('is_read', 0)->countAllResults();
    }
}
