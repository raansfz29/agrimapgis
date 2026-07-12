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

    // Disable auto timestamp management — lands table only has created_at, no updated_at
    protected $useTimestamps = false;

    /**
     * Get lands with geometry as GeoJSON
     */
    public function getLandsGeoJSON($id_lahan = null)
    {
        $builder = $this->builder();
        $builder->select('id_lahan, id_kelompok, id_user, nama_lahan, komoditas, status_fase, status_bencana, alamat, luas, latitude, longitude, created_at');
        $builder->select('ST_AsGeoJSON(geom, 6, 0) as geojson');
        
        if ($id_lahan !== null) {
            $builder->where('id_lahan', $id_lahan);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Update land's status_fase. 
     * Only upgrades the phase — never downgrades (e.g. won't go panen→persiapan).
     */
    public function updateLandFase(int $idLahan, string $newFase): bool
    {
        // Phase progression order — higher index = later stage
        $order = ['persiapan', 'tanam', 'pemeliharaan', 'panen', 'bera'];

        $land = $this->find($idLahan);
        if (!$land) return false;

        $currentFase = $land['status_fase'] ?? 'persiapan';

        // Allow downgrade only to 'persiapan' (new cycle after bera)
        $currentIdx = array_search($currentFase, $order);
        $newIdx     = array_search($newFase, $order);

        // Only update if new phase is same or later than current (forward only)
        if ($newIdx !== false && ($currentIdx === false || $newIdx >= $currentIdx)) {
            $db = \Config\Database::connect();
            $db->table($this->table)
               ->where($this->primaryKey, $idLahan)
               ->update(['status_fase' => $newFase]);
            return true;
        }

        return false;
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
                // For SRID 4326 in MySQL 8, ST_Area returns square meters natively.
                $areaSql = "SELECT ST_Area(ST_GeomFromGeoJSON(?)) / 10000 as area";
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
     * Update land with Polygon GeoJSON
     */
    public function updateLandWithGeoJSON($id, $data, $geojson)
    {
        $db = \Config\Database::connect();
        
        $sql = "UPDATE {$this->table} 
                SET id_kelompok = ?, id_user = ?, nama_lahan = ?, komoditas = ?, alamat = ?, status_fase = ?, geom = ST_GeomFromGeoJSON(?), luas = ?, latitude = ?, longitude = ? 
                WHERE id_lahan = ?";
        
        // Calculate area if not provided
        $luas = $data['luas'] ?? 0;
        if ($luas <= 0) {
            try {
                // For SRID 4326 in MySQL 8, ST_Area returns square meters natively.
                $areaSql = "SELECT ST_Area(ST_GeomFromGeoJSON(?)) / 10000 as area";
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
            $data['longitude'] ?? null,
            (int)$id
        ];

        $result = $db->query($sql, $params);
        
        if (!$result) {
            $error = $db->error();
            throw new \CodeIgniter\Database\Exceptions\DatabaseException('Gagal update lahan ke database: ' . ($error['message'] ?? 'Unknown Error'));
        }

        return true;
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

        // Historical average: rata-rata hasil panen nyata untuk komoditas ini
        // Gunakan raw query agar LOWER() tidak bermasalah dengan CI4 query builder
        $histRaw = $db->query(
            "SELECT SUM(a.hasil_panen) as total_yield, SUM(l.luas) as total_area
             FROM activities a
             JOIN lands l ON l.id_lahan = a.id_lahan
             WHERE a.jenis_aktivitas = 'panen'
               AND a.status = 'approved'
               AND a.hasil_panen > 0
               AND LOWER(l.komoditas) LIKE ?",
            ['%' . $commodity . '%']
        );
        $histQuery = $histRaw ? $histRaw->getRowArray() : null;

        if ($histQuery && $histQuery['total_area'] > 0 && $histQuery['total_yield'] > 0) {
            $historicalAvg = (float)$histQuery['total_yield'] / (float)$histQuery['total_area'];
            // Cap sanity bounds (0.5 to 15 ton/ha) to prevent data skewing
            if ($historicalAvg >= 0.5 && $historicalAvg <= 15.0) {
                $yieldPerHa = $historicalAvg;
            }
        }

        // 1. Dapatkan tanggal PANEN terakhir (batas akhir siklus sebelumnya)
        $latestPanenRaw = $db->query(
            "SELECT MAX(tanggal) as tgl FROM activities WHERE id_lahan = ? AND jenis_aktivitas = 'panen' AND status = 'approved'",
            [(int)$id_lahan]
        );
        $panenRow = $latestPanenRaw ? $latestPanenRaw->getRowArray() : null;
        $latestPanen = $panenRow ? $panenRow['tgl'] : null;

        // 2. Cari aktivitas TANAM terakhir di SIKLUS INI (setelah panen terakhir)
        $plantingQuery = "SELECT * FROM activities 
                          WHERE id_lahan = ? 
                            AND LOWER(jenis_aktivitas) IN ('tanam', 'penanaman') 
                            AND status = 'approved'";
        $params = [(int)$id_lahan];

        if ($latestPanen) {
            $plantingQuery .= " AND tanggal > ?";
            $params[] = $latestPanen;
        }
        $plantingQuery .= " ORDER BY tanggal DESC LIMIT 1";

        $plantingRaw = $db->query($plantingQuery, $params);
        $planting = $plantingRaw ? $plantingRaw->getRowArray() : null;

        // 3. Fallback: Jika tidak ada aktivitas tanam formal di siklus ini
        if (!$planting) {
            // Cari aktivitas pertanian APAPUN yang terjadi setelah panen terakhir
            // Kita kecualikan 'panen' dan 'bencana' karena petani bisa mengisi jenis_aktivitas secara custom (misal: 'Pemupukan NPK', 'pemeliharaan')
            $anyQuery = "SELECT MIN(tanggal) as tgl_pertama FROM activities 
                         WHERE id_lahan = ? 
                           AND status = 'approved' 
                           AND LOWER(jenis_aktivitas) NOT LIKE '%panen%' 
                           AND LOWER(jenis_aktivitas) NOT LIKE '%bencana%'";
            $anyParams = [(int)$id_lahan];

            if ($latestPanen) {
                $anyQuery .= " AND tanggal > ?";
                $anyParams[] = $latestPanen;
            }

            $anyRaw = $db->query($anyQuery, $anyParams);
            $anyRow = $anyRaw ? $anyRaw->getRowArray() : null;

            if ($anyRow && !empty($anyRow['tgl_pertama']) 
                && in_array($land['status_fase'], ['tanam', 'pemeliharaan', 'panen'])) {
                // Ada aktivitas pertanian di siklus baru ini -> gunakan sebagai asumsi tanggal tanam
                $plantingDate = new \DateTime($anyRow['tgl_pertama']);
                $harvestDate  = (clone $plantingDate)->add(new \DateInterval('P' . $duration . 'D'));
                $sourceLabel  = 'aktivitas_pertama';
            } elseif (!$latestPanen && in_array($land['status_fase'], ['tanam', 'pemeliharaan', 'panen'])) {
                // Jika lahan BARU SAJA dibuat (belum pernah panen sama sekali), gunakan created_at
                $plantingDate = new \DateTime($land['created_at']);
                $harvestDate  = (clone $plantingDate)->add(new \DateInterval('P' . $duration . 'D'));
                $sourceLabel  = 'status_fase';

                if ($land['status_fase'] === 'panen' && $harvestDate > new \DateTime()) {
                    $harvestDate = new \DateTime();
                }
            } else {
                // Lahan sudah pernah panen, sedang fase aktif, tapi BELUM ada aktivitas di siklus ini
                return null; 
            }
        } else {
            // Menggunakan tanggal tanam dari log tanam formal siklus ini
            $plantingDate = new \DateTime($planting['tanggal']);
            $harvestDate  = (clone $plantingDate)->add(new \DateInterval('P' . $duration . 'D'));
            $sourceLabel  = 'aktivitas_tanam';
        }

        $today       = new \DateTime();
        $diff        = $today->diff($harvestDate);
        $hariTersisa = ($harvestDate >= $today) ? (int)$diff->days : -(int)$diff->days;

        // Koreksi luas anomali (cap 100 Ha untuk hindari angka absurd)
        $luasValid = (float)$land['luas'];
        if ($luasValid > 100) {
            $luasValid = $luasValid / 1000;
        }

        return [
            'tanggal_panen' => $harvestDate->format('Y-m-d'),
            'hari_tersisa'  => $hariTersisa,
            'total_yield'   => round($luasValid * $yieldPerHa, 2),
            'satuan'        => 'ton',
            'komoditas'     => $land['komoditas'],
            'nama_lahan'    => $land['nama_lahan'],
            'source'        => $sourceLabel,
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
        
        // First, check the exact SRID of this specific land to determine axis ordering
        $sridSql = "SELECT ST_SRID(geom) as srid FROM lands WHERE id_lahan = ?";
        $sridRow = $db->query($sridSql, [$id_lahan])->getRowArray();
        $srid = $sridRow ? (int)$sridRow['srid'] : 0;
        
        // MySQL 8 strict mode enforces (Latitude Longitude) order for SRID 4326.
        // For SRID 0, it expects pure Cartesian (Longitude Latitude).
        if ($srid === 4326) {
            $ptWKT = "POINT({$latitude} {$longitude})";
        } else {
            $ptWKT = "POINT({$longitude} {$latitude})";
        }
        
        // Match the SRID dynamically to prevent "different srids" exceptions.
        // If the SRID is 4326, MySQL natively calculates distance in meters. If 0, in degrees (must multiply by 111319).
        $sql = "SELECT ST_Contains(geom, ST_GeomFromText('{$ptWKT}', {$srid})) as is_inside,
                       IF({$srid} = 4326, 
                          ST_Distance(geom, ST_GeomFromText('{$ptWKT}', {$srid})), 
                          ST_Distance(geom, ST_GeomFromText('{$ptWKT}', {$srid})) * 111319
                       ) as distance
                FROM lands WHERE id_lahan = ?";
        
        return $db->query($sql, [$id_lahan])->getRowArray();
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