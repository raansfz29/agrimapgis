<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAgriTables extends Migration
{
    public function up()
    {
        // 1) Farmer Groups (harus dibuat dulu)
        $this->forge->addField([
            'id_kelompok' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_kelompok' => ['type' => 'VARCHAR', 'constraint' => 100],
            'ketua' => ['type' => 'VARCHAR', 'constraint' => 100],
            'kecamatan' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Rajabasa'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_kelompok');
        $this->forge->createTable('farmer_groups');

        // 2) Users (harus dibuat dulu, dengan FK ke farmer_groups)
        $this->forge->addField([
            'id_user' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'ENUM', 'constraint' => ['petani', 'ppl', 'admin'], 'default' => 'petani'],
            'id_kelompok' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'telepon' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id_user');
        $this->forge->addForeignKey('id_kelompok', 'farmer_groups', 'id_kelompok', '', 'SET NULL');
        $this->forge->createTable('users');

        // 3) Lands - Create with simple geometry as VARCHAR first
        try {
            $this->db->query("
                CREATE TABLE lands (
                    id_lahan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    id_kelompok INT UNSIGNED,
                    nama_lahan VARCHAR(100),
                    komoditas ENUM('padi', 'jagung') NOT NULL,
                    alamat VARCHAR(255) NULL,
                    geom LONGTEXT,
                    luas DECIMAL(10,4) DEFAULT 0,
                    status_fase ENUM('persiapan','tanam','pemeliharaan','panen','bera','darurat') DEFAULT 'persiapan',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        } catch (\Throwable $e) {
            log_message('error', 'Failed to create lands table: ' . $e->getMessage());
        }

        // 4) Activities - Create ONLY if we need it, with or without FK
        try {
            // Try to create with FK if lands exists
            $tablesQuery = $this->db->query("SHOW TABLES LIKE 'lands'");
            if ($tablesQuery->getResultArray()) {
                // Lands exists, create with FK
                $this->forge->addField([
                    'id_aktivitas' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                    'id_lahan' => ['type' => 'INT', 'unsigned' => true],
                    'id_user' => ['type' => 'INT', 'unsigned' => true],
                    'jenis_aktivitas' => ['type' => 'VARCHAR', 'constraint' => 50],
                    'tanggal' => ['type' => 'DATE'],
                    'deskripsi' => ['type' => 'TEXT', 'null' => true],
                    'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                    'koordinat' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                    'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
                $this->forge->addPrimaryKey('id_aktivitas');
                $this->forge->addForeignKey('id_lahan', 'lands', 'id_lahan', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
                $this->forge->createTable('activities');
            } else {
                // Lands doesn't exist, create without FK
                $this->db->query("
                    CREATE TABLE activities (
                        id_aktivitas INT AUTO_INCREMENT PRIMARY KEY,
                        id_lahan INT NOT NULL,
                        id_user INT NOT NULL,
                        jenis_aktivitas VARCHAR(50) NOT NULL,
                        tanggal DATE NOT NULL,
                        deskripsi TEXT,
                        foto VARCHAR(255),
                        koordinat VARCHAR(255),
                        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ");
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to create activities table: ' . $e->getMessage());
        }
    }

    public function down()
    {
        $this->forge->dropTable('activities');
        $this->forge->dropTable('lands');
        $this->forge->dropTable('users');
        $this->forge->dropTable('farmer_groups');
    }
}
