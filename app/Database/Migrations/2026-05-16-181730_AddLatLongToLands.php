<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLatLongToLands extends Migration
{
    public function up()
    {
        $fields = [
            'latitude'  => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true, 'after' => 'luas'],
            'longitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true, 'after' => 'latitude'],
        ];
        $this->forge->addColumn('lands', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('lands', ['latitude', 'longitude']);
    }
}
