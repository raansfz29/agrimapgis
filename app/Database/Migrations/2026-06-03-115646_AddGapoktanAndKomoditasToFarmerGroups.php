<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGapoktanAndKomoditasToFarmerGroups extends Migration
{
    public function up()
    {
        $fields = [];
        
        if (! $this->db->fieldExists('gapoktan', 'farmer_groups')) {
            $fields['gapoktan'] = [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ];
        }

        if (! $this->db->fieldExists('komoditas', 'farmer_groups')) {
            $fields['komoditas'] = [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('farmer_groups', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('gapoktan', 'farmer_groups')) {
            $this->forge->dropColumn('farmer_groups', 'gapoktan');
        }
        if ($this->db->fieldExists('komoditas', 'farmer_groups')) {
            $this->forge->dropColumn('farmer_groups', 'komoditas');
        }
    }
}
