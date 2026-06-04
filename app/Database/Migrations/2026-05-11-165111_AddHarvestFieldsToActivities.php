<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHarvestFieldsToActivities extends Migration
{
    public function up()
    {
        $this->forge->addColumn('activities', [
            'hasil_panen' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'after' => 'jenis_aktivitas'],
            'satuan' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'hasil_panen'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('activities', ['hasil_panen', 'satuan']);
    }
}
