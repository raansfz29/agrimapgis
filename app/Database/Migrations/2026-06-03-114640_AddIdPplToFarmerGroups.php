<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdPplToFarmerGroups extends Migration
{
    public function up()
    {
        $fields = [
            'id_ppl' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'kecamatan'
            ]
        ];
        $this->forge->addColumn('farmer_groups', $fields);
        
        try {
            $this->db->query('ALTER TABLE farmer_groups ADD CONSTRAINT fk_farmer_groups_ppl FOREIGN KEY (id_ppl) REFERENCES users(id_user) ON DELETE SET NULL ON UPDATE SET NULL');
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
        }
    }

    public function down()
    {
        try {
            $this->db->query('ALTER TABLE farmer_groups DROP FOREIGN KEY fk_farmer_groups_ppl');
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
        }
        $this->forge->dropColumn('farmer_groups', 'id_ppl');
    }
}
