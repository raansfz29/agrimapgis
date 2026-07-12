<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityModel extends Model
{
    protected $table            = 'activities';
    protected $primaryKey       = 'id_aktivitas';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id_lahan', 
        'id_user', 
        'jenis_aktivitas', 
        'hasil_panen',
        'satuan',
        'tanggal', 
        'deskripsi', 
        'foto', 
        'koordinat',
        'status', 
        'created_at'
    ];

    /**
     * Get activities with coordinate as GeoJSON
     */
    public function getActivitiesGeoJSON($id_lahan = null)
    {
        $builder = $this->builder();
        $builder->select('id_aktivitas, id_lahan, id_user, jenis_aktivitas, tanggal, deskripsi, foto, status, created_at');
        $builder->select('ST_AsGeoJSON(koordinat, 6, 2) as geojson_koordinat');
        
        if ($id_lahan !== null) {
            $builder->where('id_lahan', $id_lahan);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Insert activity with Point Coordinate
     * $pointGeoJSON should be a valid GeoJSON Point string
     */
    public function insertActivityWithPoint($data, $pointGeoJSON = null)
    {
        $db = \Config\Database::connect();
        
        if ($pointGeoJSON) {
            $sql = "INSERT INTO {$this->table} (id_lahan, id_user, jenis_aktivitas, tanggal, deskripsi, foto, status, koordinat) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ST_GeomFromGeoJSON(?, 2, 4326))";
            
            $db->query($sql, [
                $data['id_lahan'],
                $data['id_user'],
                $data['jenis_aktivitas'],
                $data['tanggal'],
                $data['deskripsi'] ?? null,
                $data['foto'] ?? null,
                $data['status'] ?? 'pending',
                $pointGeoJSON
            ]);
            
            return $db->insertID();
        }

        return $this->insert($data);
    }
}
