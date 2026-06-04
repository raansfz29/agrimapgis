<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserToLands extends Migration
{
    public function up()
    {
        // Add id_user to lands table if it doesn't exist
        $fields = [
            'id_user' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_kelompok'
            ]
        ];
        
        // We use try-catch or check column existence because I'm not 100% sure if it's already there
        if (!$this->db->fieldExists('id_user', 'lands')) {
            $this->forge->addColumn('lands', $fields);
            $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'SET NULL');
        }
    }

    public function down()
    {
        $this->forge->dropColumn('lands', 'id_user');
    }
}
