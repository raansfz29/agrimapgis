<?php

namespace App\Models;

use CodeIgniter\Model;

class LandModel extends Model
{
    protected $table            = 'lands';
    protected $primaryKey       = 'id_lahan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // geom dan luas are handled specifically since they involve spatial functions
    protected $allowedFields    = [
        'id_kelompok', 
        'id_user',
        'nama_lahan', 
        'komoditas', 
        'alamat',
        'luas',
        'latitude',
        'longitude',
        'status_fase',
        'status_bencana',
        'foto_bencana',
        'deskripsi_bencana',
        'tanggal_bencana',
        'created_at'
    ];

    /**
     * Get lands with geometry as GeoJSON
     */
    public function getLandsGeoJSON($id_lahan = null)
    {
        $builder = $this->builder();
        $builder->select('id_lahan, id_kelompok, id_user, nama_lahan, komoditas, status_fase, status_bencana, alamat, luas, latitude, longitude, created_at');
        $builder->select('ST_AsGeoJSON(geom, 6, 2) as geojson');
        
        if ($id_lahan !== null) {
            $builder->where('id_lahan', $id_lahan);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Insert land with Polygon GeoJSON
     */
    public function insertLandWithGeoJSON($data, $geojson)
    {
        $db = \Config\Database::connect();
        
        // Use a more compatible way to insert spatial data
        // Use a more compatible way to insert spatial data
        $sql = "INSERT INTO {$this->table} (id_kelompok, id_user, nama_lahan, komoditas, alamat, status_fase, geom, luas, latitude, longitude) 
                VALUES (?, ?, ?, ?, ?, ?, ST_GeomFromGeoJSON(?), ?, ?, ?)";
        
        // Calculate area if not provided
        $luas = $data['luas'] ?? 0;
        if ($luas <= 0) {
            try {
                $areaSql = "SELECT ST_Area(ST_GeomFromGeoJSON(?)) * 111319 * 111319 / 10000 as area";
                $res = $db->query($areaSql, [$geojson])->getRowArray();
                $luas = $res['area'] ?? 0;
            } catch (\Exception $e) {
                $luas = 0;
            }
        }

        $params = [
            (int)$data['id_kelompok'],
            isset($data['id_user']) && !empty($data['id_user']) ? (int)$data['id_user'] : null,
            $data['nama_lahan'],
            $data['komoditas'],
            $data['alamat'] ?? '',
            $data['status_fase'] ?? 'persiapan',
            $geojson,
            (float)$luas,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null
        ];

        $result = $db->query($sql, $params);
        
        if (!$result) {
            $error = $db->error();
            throw new \CodeIgniter\Database\Exceptions\DatabaseException('Gagal simpan lahan ke database: ' . ($error['message'] ?? 'Unknown Error'));
        }

        return $db->insertID();
    }

    /**
     * Get summary statistics by farmer group or specific user
     */
    public function getSummaryByKelompok($id_kelompok = null, $id_user = null)
    {
        $builder = $this->builder();
        
        if ($id_kelompok !== null) {
            if (is_array($id_kelompok)) {
                $builder->whereIn('id_kelompok', $id_kelompok);
            } else {
                $builder->where('id_kelompok', $id_kelompok);
            }
        }

        if ($id_user !== null) {
            $builder->where('id_user', $id_user);
        }
        
        // Get total lands count
        $totalLands = $builder->countAllResults(false);
        
        // Get total area and commodity breakdown
        $builder->select('komoditas, SUM(luas) as total_luas');
        $builder->groupBy('komoditas');
        $commodityData = $builder->get()->getResultArray();
        
        $totalLuas = 0;
        $commodities = [];
        foreach ($commodityData as $row) {
            $totalLuas += $row['total_luas'];
            $commodities[$row['komoditas']] = $row['total_luas'];
        }
        
        return [
            'total_lands' => $totalLands,
            'total_luas' => $totalLuas,
            'commodities' => $commodities
        ];
    }

    /**
     * Get lands list by farmer group without geometry
     */
    public function getLandsByKelompok($id_kelompok = null)
    {
        $builder = $this->builder();
        $builder->select('id_lahan, nama_lahan, komoditas, status_fase, status_bencana, luas, created_at');
        
        if ($id_kelompok !== null) {
            $builder->where('id_kelompok', $id_kelompok);
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Set disaster status for a land
     */
    public function setDisasterStatus($id_lahan, $status, $deskripsi = null, $foto = null)
    {
        $data = [
            'status_bencana' => $status,
            'tanggal_bencana' => date('Y-m-d H:i:s')
        ];

        if ($status === 'normal') {
            $data['deskripsi_bencana'] = null;
            $data['foto_bencana'] = null;
        } else {
            if ($deskripsi !== null) {
                $data['deskripsi_bencana'] = $deskripsi;
            }
            if ($foto !== null) {
                $data['foto_bencana'] = $foto;
            }
        }

        return $this->update($id_lahan, $data);
    }

    /**
     * Get lands with disaster status
     */
    public function getDisasterLands($id_kelompok = null)
    {
        $builder = $this->builder();
        $builder->select('id_lahan, id_kelompok, nama_lahan, komoditas, status_fase, status_bencana, foto_bencana, deskripsi_bencana, tanggal_bencana');
        $builder->where('status_bencana', 'darurat');

        if ($id_kelompok !== null) {
            if (is_array($id_kelompok)) {
                $builder->whereIn('id_kelompok', $id_kelompok);
            } else {
                $builder->where('id_kelompok', $id_kelompok);
            }
        }

        $builder->orderBy('tanggal_bencana', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get harvest prediction for a land based on latest approved planting activity
     */
    public function getHarvestPrediction($id_lahan)
    {
        $db  = \Config\Database::connect();
        $land = $this->find($id_lahan);
        if (!$land) return null;

        $commodity = strtolower($land['komoditas']);
        $duration  = 100; // default hari
        $yieldPerHa = 5.0; // ton/ha default

        if (strpos($commodity, 'padi') !== false) {
            $duration   = 110;
            $yieldPerHa = 5.5;
        } elseif (strpos($commodity, 'jagung') !== false) {
            $duration   = 90;
            $yieldPerHa = 6.0;
        } elseif (strpos($commodity, 'kedelai') !== false) {
            $duration   = 85;
            $yieldPerHa = 1.5;
        }

        // Cari aktivitas 'tanam' yang approved untuk dapatkan tanggal tanam (case-insensitive)
        $planting = $db->table('activities')
            ->where('id_lahan', $id_lahan)
            ->whereIn('LOWER(jenis_aktivitas)', ['tanam', 'penanaman'])
            ->where('status', 'approved')
            ->orderBy('tanggal', 'DESC')
            ->get()->getRowArray();

        // Fallback: jika fase lahan = 'panen' tapi tak ada aktivitas tanam,
        // anggap tanggal tanam adalah (hari ini - duration hari)
        if (!$planting) {
            if ($land['status_fase'] === 'panen') {
                // Lahan sudah di fase panen — estimasi panen = hari ini
                $harvestDate  = new \DateTime();
                $plantingDate = (clone $harvestDate)->sub(new \DateInterval('P' . $duration . 'D'));
            } else {
                return null; // Belum tanam, tidak bisa prediksi
            }
        } else {
            $plantingDate = new \DateTime($planting['tanggal']);
            $harvestDate  = (clone $plantingDate)->add(new \DateInterval('P' . $duration . 'D'));
        }

        $today      = new \DateTime();
        $diff       = $today->diff($harvestDate);
        $hariTersisa = ($harvestDate >= $today) ? (int)$diff->days : -(int)$diff->days;

        // Fix anomalous area size (cap at 100 Ha for prediction logic to avoid absurd numbers)
        $luasValid = (float)$land['luas'];
        if ($luasValid > 100) {
            // Assume input error like 1007.11 instead of 1.00711
            $luasValid = $luasValid / 1000;
        }

        return [
            'tanggal_panen' => $harvestDate->format('Y-m-d'),
            'hari_tersisa'  => $hariTersisa,
            'total_yield'   => round($luasValid * $yieldPerHa, 2),
            'satuan'        => 'ton',
            'komoditas'     => $land['komoditas'],
            'nama_lahan'    => $land['nama_lahan'],
            'source'        => $planting ? 'aktivitas_tanam' : 'status_fase',
        ];
    }

    /**
     * Check if a coordinate point is within a land polygon (with 50m tolerance)
     */
    public function getGeofencingResult($id_lahan, $longitude, $latitude)
    {
        $db = \Config\Database::connect();
        $pointGeoJSON = json_encode([
            'type' => 'Point',
            'coordinates' => [(float)$longitude, (float)$latitude]
        ]);
        
        // Force SRID to 0 to match the geom column in lands table
        $sql = "SELECT ST_Contains(geom, ST_GeomFromGeoJSON(?, 2, 0)) as is_inside,
                       (ST_Distance(geom, ST_GeomFromGeoJSON(?, 2, 0)) * 111319) as distance
                FROM lands WHERE id_lahan = ?";
        
        return $db->query($sql, [$pointGeoJSON, $pointGeoJSON, $id_lahan])->getRowArray();
    }

    public function isWithinLand($id_lahan, $longitude, $latitude)
    {
        $result = $this->getGeofencingResult($id_lahan, $longitude, $latitude);
        
        if ($result && ($result['is_inside'] == 1 || $result['distance'] <= 100)) {
            return true;
        }
        
        return false;
    }
}