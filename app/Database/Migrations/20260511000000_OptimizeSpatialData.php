<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OptimizeSpatialData extends Migration
{
    public function up()
    {
        // 1. First, we need to ensure geom is not NULL for SPATIAL INDEX
        // We will temporarily change it to GEOMETRY if it's currently LONGTEXT
        
        $this->db->query("ALTER TABLE lands MODIFY geom GEOMETRY NOT NULL");
        
        // 2. Add SPATIAL INDEX
        $this->db->query("ALTER TABLE lands ADD SPATIAL INDEX(geom)");
    }

    public function down()
    {
        // Remove spatial index
        $this->db->query("ALTER TABLE lands DROP INDEX geom");
        
        // Revert to LONGTEXT
        $this->db->query("ALTER TABLE lands MODIFY geom LONGTEXT");
    }
}
