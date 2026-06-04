<?php

namespace App\Models;

use CodeIgniter\Model;

class DisasterLogModel extends Model
{
    protected $table            = 'disaster_logs';
    protected $primaryKey       = 'id_log';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_lahan',
        'id_user',
        'judul_kejadian',
        'jenis_bencana',
        'deskripsi_kejadian',
        'luas_terdampak',
        'estimasi_kerugian',
        'tindakan_diambil',
        'status_penanganan',
        'foto',
    ];

    protected $useTimestamps = false; // using manual created_at

    /**
     * Get logs with land and user name, optionally filtered by kelompok
     */
    public function getLogsWithDetails($id_kelompok = null)
    {
        $builder = $this->db->table('disaster_logs dl');
        $builder->select('dl.*, l.nama_lahan, l.komoditas, u.nama as nama_petugas');
        $builder->join('lands l', 'l.id_lahan = dl.id_lahan');
        $builder->join('users u', 'u.id_user = dl.id_user');

        if ($id_kelompok) {
            $builder->where('l.id_kelompok', $id_kelompok);
        }

        $builder->orderBy('dl.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }
}
