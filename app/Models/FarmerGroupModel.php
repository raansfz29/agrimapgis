<?php

namespace App\Models;

use CodeIgniter\Model;

class FarmerGroupModel extends Model
{
    protected $table            = 'farmer_groups';
    protected $primaryKey       = 'id_kelompok';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['nama_kelompok', 'ketua', 'id_ppl', 'kecamatan', 'created_at', 'komoditas', 'gapoktan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
