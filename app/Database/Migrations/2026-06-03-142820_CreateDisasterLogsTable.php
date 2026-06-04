<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDisasterLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_lahan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'judul_kejadian' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'jenis_bencana' => [
                'type'       => 'ENUM',
                'constraint' => ['banjir', 'kekeringan', 'hama', 'angin_kencang', 'lainnya'],
                'default'    => 'banjir',
            ],
            'deskripsi_kejadian' => [
                'type' => 'TEXT',
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'luas_terdampak' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ],
            'estimasi_kerugian' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'tindakan_diambil' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_penanganan' => [
                'type'       => 'ENUM',
                'constraint' => ['dalam_penanganan', 'selesai', 'butuh_bantuan'],
                'default'    => 'dalam_penanganan',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id_log', true);
        $this->forge->addForeignKey('id_lahan', 'lands', 'id_lahan', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('disaster_logs');
    }

    public function down()
    {
        $this->forge->dropTable('disaster_logs');
    }
}
