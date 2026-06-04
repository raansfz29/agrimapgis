<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'id_pesan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id_pengirim',
        'id_penerima',
        'isi_pesan',
        'is_read',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getConversation($user1, $user2)
    {
        return $this->groupStart()
                        ->where(['id_pengirim' => $user1, 'id_penerima' => $user2])
                    ->groupEnd()
                    ->orGroupStart()
                        ->where(['id_pengirim' => $user2, 'id_penerima' => $user1])
                    ->groupEnd()
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getUnreadCount($userId)
    {
        return $this->where('id_penerima', $userId)->where('is_read', 0)->countAllResults();
    }
}
