<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestWorkflow extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Testing';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'test:workflow';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Runs an end-to-end integration test of all major workflows using dummy data.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'test:workflow';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write("=========================================", 'yellow');
        CLI::write("🚀 INITIATING END-TO-END WORKFLOW TESTING", 'yellow');
        CLI::write("=========================================\n", 'yellow');

        $userModel = new \App\Models\UserModel();
        $groupModel = new \App\Models\FarmerGroupModel();
        $landModel = new \App\Models\LandModel();
        $activityModel = new \App\Models\ActivityModel();
        $disasterModel = new \App\Models\DisasterLogModel();
        $notifModel = new \App\Models\NotificationModel();
        $db = \Config\Database::connect();

        try {
            // ==========================================
            // PHASE A: INITIALIZATION & DATA SETUP
            // ==========================================
            CLI::write("▶ PHASE A: Setting up dummy data...", 'cyan');

            // 1. Create PPL User
            $pplId = $userModel->insert([
                'nama' => 'DUMMY_PPL_USER',
                'email' => 'dummyppl_' . time() . '@test.com',
                'no_hp' => '081111111111',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'ppl'
            ]);
            CLI::write("  [OK] Created PPL User (ID: {$pplId})", 'green');

            // 2. Create Farmer Group
            $groupId = $groupModel->insert([
                'nama_kelompok' => 'DUMMY_KELOMPOK_TANI',
                'desa' => 'Desa Dummy',
                'id_ppl' => $pplId
            ]);
            CLI::write("  [OK] Created Farmer Group (ID: {$groupId})", 'green');

            // 3. Create Petani User
            $petaniId = $userModel->insert([
                'nama' => 'DUMMY_PETANI_USER',
                'email' => 'dummypetani_' . time() . '@test.com',
                'no_hp' => '082222222222',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'petani',
                'id_kelompok' => $groupId
            ]);
            CLI::write("  [OK] Created Petani User (ID: {$petaniId})", 'green');

            // 4. Create Land
            $landId = $landModel->insertLandWithGeoJSON([
                'id_kelompok' => $groupId,
                'id_user' => $petaniId,
                'nama_lahan' => 'DUMMY_LAHAN_JAGUNG',
                'komoditas' => 'jagung',
                'luas' => 2.5,
                'alamat' => 'Alamat Dummy',
                'status_fase' => 'persiapan',
                'latitude' => -5.15,
                'longitude' => 105.15
            ], '{"type":"Polygon","coordinates":[[[105.1,-5.1],[105.2,-5.1],[105.2,-5.2],[105.1,-5.2],[105.1,-5.1]]]}');
            
            CLI::write("  [OK] Created Land (ID: {$landId})", 'green');


            // ==========================================
            // PHASE B: PLANTING & HARVEST PREDICTION
            // ==========================================
            CLI::write("\n▶ PHASE B: Testing Planting & Harvest Prediction...", 'cyan');

            // 1. Create Planting Activity (Status: Pending by default)
            $activityId = $activityModel->insert([
                'id_lahan' => $landId,
                'id_user' => $petaniId,
                'jenis_aktivitas' => 'Penanaman',
                'tanggal' => date('Y-m-d', strtotime('-80 days')), // Planted 80 days ago (corn takes 90 days, 10 days left)
                'status' => 'pending'
            ]);
            CLI::write("  [OK] Created Planting Activity (ID: {$activityId})", 'green');

            // Prediction should fail because activity is pending
            $pred1 = $landModel->getHarvestPrediction($landId);
            if ($pred1 === null) {
                CLI::write("  [OK] Harvest prediction correctly returned null for pending activity.", 'green');
            } else {
                CLI::error("  [FAIL] Harvest prediction returned data for pending activity!");
                throw new \Exception("Harvest Prediction Logic Error");
            }

            // 2. Approve Activity (PPL action)
            $activityModel->update($activityId, ['status' => 'approved']);
            CLI::write("  [OK] Approved Planting Activity", 'green');

            // 3. Check Prediction again
            $pred2 = $landModel->getHarvestPrediction($landId);
            if ($pred2 !== null && $pred2['hari_tersisa'] > 0 && $pred2['hari_tersisa'] <= 11) {
                CLI::write("  [OK] Harvest prediction calculated correctly ({$pred2['hari_tersisa']} days remaining).", 'green');
            } else {
                CLI::error("  [FAIL] Harvest prediction incorrect or null! Dump: " . print_r($pred2, true));
                throw new \Exception("Harvest Prediction Logic Error");
            }


            // ==========================================
            // PHASE C: DISASTER MITIGATION WORKFLOW
            // ==========================================
            CLI::write("\n▶ PHASE C: Testing Disaster Mitigation Workflow...", 'cyan');

            // 1. Report Disaster
            $disasterActId = $activityModel->insert([
                'id_lahan' => $landId,
                'id_user' => $petaniId,
                'jenis_aktivitas' => 'Riwayat Bencana',
                'tanggal' => date('Y-m-d'),
                'deskripsi' => 'DUMMY Bencana Banjir',
                'status' => 'pending'
            ]);
            CLI::write("  [OK] Reported Disaster Activity (ID: {$disasterActId})", 'green');

            // 2. PPL verifies and sets land to 'darurat'
            $activityModel->update($disasterActId, ['status' => 'approved']);
            $landModel->update($landId, ['status_fase' => 'darurat']);
            $disasterLogId = $disasterModel->insert([
                'id_lahan' => $landId,
                'id_user' => $pplId,
                'judul_kejadian' => 'DUMMY Bencana Banjir',
                'jenis_bencana' => 'Banjir',
                'deskripsi_kejadian' => 'Sedang',
                'status_penanganan' => 'aktif'
            ]);
            CLI::write("  [OK] PPL verified disaster and updated Land status to 'darurat'.", 'green');

            // 3. PPL resolves disaster
            $disasterModel->update($disasterLogId, ['status_penanganan' => 'selesai']);
            $landModel->update($landId, ['status_fase' => 'tanam']); // Back to normal
            CLI::write("  [OK] PPL resolved the disaster and returned Land status to normal.", 'green');


            // ==========================================
            // PHASE D: NOTIFICATION SYSTEM
            // ==========================================
            CLI::write("\n▶ PHASE D: Testing Notifications...", 'cyan');
            
            // To test notifications generated dynamically, we can manually trigger the notification 
            // logic that would normally be inside a controller to simulate it.
            $notifModel->broadcastToGroup($groupId, 'petani', 'DUMMY_NOTIF_TITLE', 'DUMMY_NOTIF_MESSAGE', 'info');
            
            $notifs = $notifModel->where('id_user', $petaniId)->where('judul', 'DUMMY_NOTIF_TITLE')->findAll();
            if (count($notifs) > 0) {
                CLI::write("  [OK] Broadcast notification successfully delivered to Petani.", 'green');
            } else {
                CLI::error("  [FAIL] Notification not delivered!");
                throw new \Exception("Notification Logic Error");
            }


            CLI::write("\n✅ ALL TESTS PASSED SUCCESSFULLY!", 'black', 'green');

        } catch (\Exception $e) {
            CLI::error("\n❌ TEST FAILED: " . $e->getMessage());
        } finally {
            // ==========================================
            // PHASE E: DATA CLEANUP
            // ==========================================
            CLI::write("\n▶ PHASE E: Cleaning up dummy data...", 'yellow');
            
            // Delete Notifications
            $db->table('notifications')->where('judul', 'DUMMY_NOTIF_TITLE')->delete();
            // Delete Disaster Logs
            if (isset($landId)) $db->table('disaster_logs')->where('id_lahan', $landId)->delete();
            // Delete Activities
            if (isset($landId)) $db->table('activities')->where('id_lahan', $landId)->delete();
            // Delete Land
            if (isset($landId)) $landModel->delete($landId);
            // Delete Group
            if (isset($groupId)) $db->table('farmer_groups')->where('id_kelompok', $groupId)->delete();
            // Delete Users
            if (isset($petaniId)) $userModel->delete($petaniId);
            if (isset($pplId)) $userModel->delete($pplId);

            CLI::write("  [OK] Cleanup completed.", 'green');
        }
    }
}
