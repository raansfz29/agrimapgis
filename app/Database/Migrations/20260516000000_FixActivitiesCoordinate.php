<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixActivitiesCoordinate extends Migration
{
    public function up()
    {
        // Modify koordinat to be GEOMETRY instead of VARCHAR
        $this->db->query("ALTER TABLE activities MODIFY koordinat GEOMETRY NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE activities MODIFY koordinat VARCHAR(255) NULL");
    }
}
