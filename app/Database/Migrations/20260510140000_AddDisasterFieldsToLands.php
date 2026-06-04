<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDisasterFieldsToLands extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lands', [
            'status_bencana' => [
                'type' => 'ENUM',
                'constraint' => ['normal', 'darurat'],
                'default' => 'normal',
                'after' => 'status_fase'
            ],
            'foto_bencana' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'status_bencana'
            ],
            'deskripsi_bencana' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'foto_bencana'
            ],
            'tanggal_bencana' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'deskripsi_bencana'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('lands', ['status_bencana', 'foto_bencana', 'deskripsi_bencana', 'tanggal_bencana']);
    }
}