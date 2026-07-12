<?php

namespace App\Controllers;

use App\Models\LandModel;
use App\Models\MessageModel;
use App\Models\NotificationModel;
use App\Models\UserModel;

class Disaster extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $userRole = session()->get('role');
        $userIdKelompok = session()->get('id_kelompok');

        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = $userIdKelompok;
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

        if ($userRole === 'admin') {
            $lands = $landModel->findAll();
            $disasterLands = $landModel->getDisasterLands();
        } elseif ($filterArray) {
            $lands = $landModel->builder()->whereIn('id_kelompok', $filterArray)->get()->getResultArray();
            $disasterLands = $landModel->getDisasterLands($filterArray);
        } else {
            $lands = [];
            $disasterLands = [];
        }

        $data = [
            'title'         => 'Mitigasi Bencana',
            'nama'          => session()->get('nama'),
            'role'          => session()->get('role'),
            'lands'         => $lands,
            'disasterLands' => $disasterLands,
        ];

        return view('disaster/index', $data);
    }

    /**
     * Broadcast a structured disaster alert message to all farmers in the group
     * AND push a critical notification to each of them.
     */
    public function broadcastAlert()
    {
        if (!session()->get('is_logged_in') || session()->get('role') === 'petani') {
            return redirect()->to('/login')->with('error', 'Akses ditolak. Hanya PPL dan Admin yang dapat menyebarkan peringatan.');
        }

        $userRole = session()->get('role');
        $senderId   = session()->get('id_user');
        $senderNama = session()->get('nama');
        
        $filterKelompok = null;
        if ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        } elseif ($userRole === 'admin') {
            // Admin can broadcast to everyone
            $filterKelompok = null;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;
        $senderId   = session()->get('id_user');
        $senderNama = session()->get('nama');

        $landModel     = new LandModel();
        if ($userRole === 'admin') {
            $disasterLands = $landModel->getDisasterLands();
        } else {
            $disasterLands = $landModel->getDisasterLands($filterArray);
        }

        if (empty($disasterLands)) {
            return redirect()->to('/disaster')->with('error', 'Tidak ada bencana aktif yang perlu dilaporkan.');
        }

        $userModel  = new UserModel();
        $msgModel   = new MessageModel();
        $notifModel = new NotificationModel();

        $notifiedUsers = [];

        foreach ($disasterLands as $land) {
            $tanggal   = date('d/m/Y H:i');
            $namaLahan = $land['nama_lahan'];
            $deskripsi = $land['deskripsi_bencana'] ?? 'Bencana terdeteksi di wilayah Rajabasa.';

            // Build structured emergency broadcast message
            $pesanDarurat = "[ SIAGA BENCANA ] — AgriMapGIS\n"
                . "================================\n"
                . " LOKASI   : {$namaLahan}\n"
                . " WAKTU    : {$tanggal}\n"
                . " KEJADIAN : {$deskripsi}\n"
                . "================================\n"
                . " TINDAKAN SEGERA:\n"
                . " 1. Segera hentikan sementara aktivitas lahan\n"
                . " 2. Lakukan dokumentasi kerusakan lahan (foto/video)\n"
                . " 3. Laporkan detail kondisi ke sistem / PPL\n"
                . "================================\n"
                . "Dikirim oleh PPL {$senderNama} via AgriMapGIS.\n"
                . "Hubungi PPL Anda untuk bantuan teknis lebih lanjut.";
                
            if (!empty($land['foto_bencana'])) {
                $pesanDarurat .= "\n\n[FOTO_BENCANA:" . $land['foto_bencana'] . "]";
            }

            $farmers = $userModel
                ->where('role', 'petani')
                ->where('id_kelompok', $land['id_kelompok'])
                ->findAll();

            foreach ($farmers as $farmer) {
                // Send direct message
                $msgModel->insert([
                    'id_pengirim' => $senderId,
                    'id_penerima' => $farmer['id_user'],
                    'isi_pesan'   => $pesanDarurat,
                    'is_read'     => 0,
                ]);

                // Push danger notification to farmer's panel
                $notifModel->createNotification(
                    $farmer['id_user'],
                    '[DARURAT] Peringatan Bencana: ' . $namaLahan,
                    "Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL {$senderNama} untuk instruksi mitigasi.",
                    'danger'
                );

                $notifiedUsers[$farmer['id_user']] = true;
            }
        }

        $sentCount = count($notifiedUsers);

        // Notify the PPL sender with a confirmation
        $notifModel->createNotification(
            $senderId,
            '[SIAGA] Siaran Darurat Terkirim',
            "Peringatan bencana berhasil disiarkan ke {$sentCount} petani. Pantau respons mereka melalui menu Pesan.",
            'warning'
        );

        return redirect()->to('/message')
            ->with('success', "Peringatan darurat berhasil dikirim ke {$sentCount} petani terdampak.");
    }

    public function activate($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $landModel = new LandModel();
        $land = $landModel->find($id);

        if (!$land) {
            return redirect()->to('/disaster')->with('error', 'Lahan tidak ditemukan.');
        }

        $userRole       = session()->get('role');
        
        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = session()->get('id_kelompok');
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

        if ($userRole !== 'admin' && $filterArray && !in_array($land['id_kelompok'], $filterArray)) {
            return redirect()->to('/disaster')->with('error', 'Anda tidak memiliki akses ke lahan ini.');
        }

        $data = [
            'title' => 'Aktivasi Status Bencana',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'land'  => $land,
        ];

        return view('disaster/activate', $data);
    }

    public function activateSubmit($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'jenis_bencana'     => 'required',
                'deskripsi_bencana' => 'required|min_length[10]|max_length[500]',
                'foto_bencana'      => 'permit_empty|max_size[foto_bencana,2048]|is_image[foto_bencana]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $landModel = new LandModel();
            $land = $landModel->find($id);

            if (!$land) {
                return redirect()->to('/disaster')->with('error', 'Lahan tidak ditemukan.');
            }

            $userRole       = session()->get('role');
            
            $filterKelompok = null;
            if ($userRole === 'petani') {
                $filterKelompok = session()->get('id_kelompok');
            } elseif ($userRole === 'ppl') {
                $groupModel = new \App\Models\FarmerGroupModel();
                $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
                $managedGroupIds = array_column($groups, 'id_kelompok');
                $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
            }
            $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

            if ($userRole !== 'admin' && $filterArray && !in_array($land['id_kelompok'], $filterArray)) {
                return redirect()->to('/disaster')->with('error', 'Anda tidak memiliki akses ke lahan ini.');
            }

            $foto     = $this->request->getFile('foto_bencana');
            $fotoName = null;

            if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                $fotoName = $foto->getRandomName();
                $foto->move(WRITEPATH . 'uploads', $fotoName);
            }

            $jenisBencana = $this->request->getPost('jenis_bencana');
            $deskripsiBencana = "[$jenisBencana] " . $this->request->getPost('deskripsi_bencana');

            $mitigasi = "";
            switch ($jenisBencana) {
                case 'Banjir':
                    $mitigasi = "1. Segera buat saluran pembuangan air (drainase) darurat.\n2. Amankan peralatan pertanian dan kelistrikan.\n3. Hindari aplikasi pupuk kimia hingga genangan surut.";
                    break;
                case 'Kekeringan':
                    $mitigasi = "1. Lakukan pengairan bergilir jika ada sumber air terdekat.\n2. Gunakan mulsa untuk menjaga kelembapan tanah.\n3. Tunda pemupukan untuk mencegah tanaman keracunan.";
                    break;
                case 'Hama Wereng':
                    $mitigasi = "1. Lakukan penyemprotan insektisida sistemik yang disarankan PPL.\n2. Kurangi volume air genangan di lahan sawah.\n3. Gunakan varietas tahan wereng untuk musim tanam berikutnya.";
                    break;
                case 'Hama Tikus':
                    $mitigasi = "1. Segera lakukan gropyokan massal bersama kelompok tani.\n2. Bersihkan gulma di pematang yang jadi sarang tikus.\n3. Aplikasikan umpan beracun sesuai instruksi PPL.";
                    break;
                case 'Penyakit Jamur':
                    $mitigasi = "1. Segera hentikan pemupukan unsur Nitrogen (Urea).\n2. Lakukan penyemprotan fungisida secara merata.\n3. Kurangi kelembapan dengan menjarangkan jarak tanam jika memungkinkan.";
                    break;
                case 'Angin Puting Beliung':
                    $mitigasi = "1. Hindari area lahan sampai cuaca benar-benar aman.\n2. Bersihkan puing-puing dan dahan roboh dengan hati-hati.\n3. Dirikan kembali tanaman yang rebah secara perlahan jika memungkinkan.";
                    break;
                default:
                    $mitigasi = "1. Hentikan sementara aktivitas rutin di lahan terdampak.\n2. Amankan hasil panen/peralatan yang masih bisa diselamatkan.\n3. Segera koordinasi dengan PPL untuk langkah teknis spesifik.";
                    break;
            }

            $updated = $landModel->setDisasterStatus(
                $id,
                'darurat',
                $deskripsiBencana,
                $fotoName
            );

            if ($updated) {
                $notifModel = new NotificationModel();
                $userModel  = new UserModel();
                $msgModel   = new MessageModel();

                $farmers = $userModel
                    ->where('role', 'petani')
                    ->where('id_kelompok', $land['id_kelompok'])
                    ->findAll();
                
                $groupModel = new \App\Models\FarmerGroupModel();
                $group = $groupModel->find($land['id_kelompok']);
                $pplId = $group ? $group['id_ppl'] : null;

                $senderId = session()->get('id_user');
                $senderNama = session()->get('nama');
                
                $pesanDarurat = "[ SIAGA BENCANA ] — Laporan Darurat\n"
                    . "================================\n"
                    . " LOKASI   : {$land['nama_lahan']}\n"
                    . " WAKTU    : " . date('d/m/Y H:i') . "\n"
                    . " KEJADIAN : {$deskripsiBencana}\n"
                    . "================================\n"
                    . " LANGKAH MITIGASI AWAL:\n"
                    . "{$mitigasi}\n"
                    . "================================\n"
                    . "Dilaporkan oleh: {$senderNama}.";
                
                if ($fotoName) {
                    $pesanDarurat .= "\n\n[FOTO_BENCANA:" . $fotoName . "]";
                }

                $penerimaIds = [];
                foreach ($farmers as $farmer) {
                    if ($farmer['id_user'] != $senderId) {
                        $penerimaIds[] = $farmer['id_user'];
                    }
                }
                if ($pplId && $pplId != $senderId) {
                    $penerimaIds[] = $pplId;
                }

                foreach ($penerimaIds as $idPenerima) {
                    // Send Direct Message
                    $msgModel->insert([
                        'id_pengirim' => $senderId,
                        'id_penerima' => $idPenerima,
                        'isi_pesan'   => $pesanDarurat,
                        'is_read'     => 0,
                    ]);

                    // Send Notification
                    $notifModel->createNotification(
                        $idPenerima,
                        '[DARURAT] Bencana: ' . $land['nama_lahan'],
                        "Lahan " . $land['nama_lahan'] . " dilaporkan mengalami bencana. Cek pesan masuk Anda untuk instruksi mitigasi awal.",
                        'danger'
                    );
                }

                // Notify the sender (reporter) too
                $notifModel->createNotification(
                    $senderId,
                    'Laporan Darurat Terkirim',
                    "Laporan darurat untuk lahan " . $land['nama_lahan'] . " berhasil disiarkan ke kelompok tani dan PPL.",
                    'success'
                );

                log_message('info', 'Disaster activated for land ' . $id . ' by ' . session()->get('nama'));
                return redirect()->to('/disaster')
                    ->with('success', 'Status bencana diaktifkan. Peringatan dan instruksi mitigasi telah disebarkan ke kelompok Anda dan PPL.');
            }

            return redirect()->back()->withInput()->with('error', 'Gagal mengaktifkan status bencana.');
        }

        return redirect()->to('/disaster');
    }

    public function log($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $land = $landModel->find($id);

        if (!$land) {
            return redirect()->to('/disaster')->with('error', 'Lahan tidak ditemukan.');
        }

        $logModel = new \App\Models\DisasterLogModel();
        // Custom join to get user names
        $db = \Config\Database::connect();
        $logs = $db->table('disaster_logs')
                   ->select('disaster_logs.*, users.nama as nama_user, users.role as role_user')
                   ->join('users', 'users.id_user = disaster_logs.id_user')
                   ->where('disaster_logs.id_lahan', $id)
                   ->orderBy('disaster_logs.created_at', 'DESC')
                   ->get()->getResultArray();

        $data = [
            'title' => 'Log Kejadian Bencana',
            'nama'  => session()->get('nama'),
            'role'  => session()->get('role'),
            'land'  => $land,
            'logs'  => $logs
        ];

        return view('disaster/log', $data);
    }

    public function submitLog($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $landModel = new LandModel();
        $land = $landModel->find($id);

        if (!$land || $land['status_bencana'] !== 'darurat') {
            return redirect()->to('/disaster')->with('error', 'Lahan tidak dalam status darurat.');
        }

        $userRole = session()->get('role');
        $rules = [
            'catatan' => 'required|min_length[5]'
        ];

        if ($userRole === 'petani') {
            $rules['foto_kejadian'] = 'uploaded[foto_kejadian]|is_image[foto_kejadian]|mime_in[foto_kejadian,image/jpg,image/jpeg,image/png,image/webp,image/heic]';
        } else {
            $rules['foto_kejadian'] = 'permit_empty|is_image[foto_kejadian]|mime_in[foto_kejadian,image/jpg,image/jpeg,image/png,image/webp,image/heic]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' | ', $this->validator->getErrors()));
        }

        $foto = $this->request->getFile('foto_kejadian');
        $fotoName = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            $foto->move(WRITEPATH . 'uploads', $fotoName);
        }

        $logModel = new \App\Models\DisasterLogModel();
        $logModel->insert([
            'id_lahan' => $id,
            'id_user'  => session()->get('id_user'),
            'judul_kejadian' => 'Update Mitigasi',
            'deskripsi_kejadian'  => $this->request->getPost('catatan'),
            'status_penanganan' => 'Dalam Proses',
            'foto' => $fotoName
        ]);

        return redirect()->to('/disaster/log/' . $id)->with('success', 'Catatan log kejadian berhasil ditambahkan.');
    }

    public function deactivate($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $landModel = new LandModel();
        $land = $landModel->find($id);

        if (!$land) {
            return redirect()->to('/disaster')->with('error', 'Lahan tidak ditemukan.');
        }

        $userRole = session()->get('role');
        $filterKelompok = null;
        if ($userRole === 'petani') {
            $filterKelompok = session()->get('id_kelompok');
        } elseif ($userRole === 'ppl') {
            $groupModel = new \App\Models\FarmerGroupModel();
            $groups = $groupModel->where('id_ppl', session()->get('id_user'))->findAll();
            $managedGroupIds = array_column($groups, 'id_kelompok');
            $filterKelompok = empty($managedGroupIds) ? [0] : $managedGroupIds;
        }
        $filterArray = $filterKelompok !== null ? (is_array($filterKelompok) ? $filterKelompok : [$filterKelompok]) : null;

        if ($userRole !== 'admin' && $filterArray && !in_array($land['id_kelompok'], $filterArray)) {
            return redirect()->to('/disaster')->with('error', 'Anda tidak memiliki akses ke lahan ini.');
        }

        $deskripsiBencana = $land['deskripsi_bencana'] ?? 'Tidak ada deskripsi';
        $fotoBencana = $land['foto_bencana'];
        $tanggalMulai = $land['tanggal_bencana'] ? date('d M Y', strtotime($land['tanggal_bencana'])) : 'Tidak diketahui';

        $updated = $landModel->setDisasterStatus($id, 'normal');

        if ($updated) {
            // Add final log entry
            $logModel = new \App\Models\DisasterLogModel();
            $logModel->insert([
                'id_lahan'     => $id,
                'id_user'      => session()->get('id_user'),
                'judul_kejadian' => 'Status Bencana Diselesaikan',
                'deskripsi_kejadian' => 'Bencana telah dinyatakan selesai dan lahan kembali aman. Panel Log Kejadian diarsipkan.',
                'status_penanganan' => 'Selesai'
            ]);

            // Record as activity history
            $activityModel = new \App\Models\ActivityModel();
            $tanggalSelesai = date('d M Y');
            
            $activityData = [
                'id_lahan' => $id,
                'id_user' => $land['id_user'] ?? session()->get('id_user'),
                'jenis_aktivitas' => 'Riwayat Bencana',
                'tanggal' => date('Y-m-d'),
                'deskripsi' => "Telah terjadi bencana dengan detail: {$deskripsiBencana}\nMasa darurat: {$tanggalMulai} s/d {$tanggalSelesai}",
                'status' => 'approved',
                'foto' => $fotoBencana
            ];

            $activityModel->insert($activityData);
            $insertedId = $activityModel->getInsertID();

            if ($insertedId) {
                // Ensure coordinate is always populated by extracting the geometric centroid of the land.
                // We strip the SRID to 0 to bypass MySQL 8's lack of ST_Centroid support for geographic maps (4326),
                // calculate the pure Cartesian center, and then wrap it back into its original SRID.
                $db = \Config\Database::connect();
                $centroidSql = "UPDATE activities 
                                SET koordinat = (
                                    SELECT ST_SRID(ST_Centroid(ST_SRID(geom, 0)), ST_SRID(geom)) 
                                    FROM lands WHERE id_lahan = ?
                                ) 
                                WHERE id_aktivitas = ?";
                $db->query($centroidSql, [$id, $insertedId]);
            }

            // Notify farmers the disaster is resolved
            $notifModel = new NotificationModel();
            $userModel  = new UserModel();

            $farmers = $userModel
                ->where('role', 'petani')
                ->where('id_kelompok', $land['id_kelompok'])
                ->findAll();

            foreach ($farmers as $farmer) {
                $notifModel->createNotification(
                    $farmer['id_user'],
                    '[AMAN] Bencana Selesai: ' . $land['nama_lahan'],
                    'Lahan ' . $land['nama_lahan'] . ' telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.',
                    'success'
                );
            }

            log_message('info', 'Disaster deactivated for land ' . $id . ' by ' . session()->get('nama'));
            return redirect()->to('/disaster')
                ->with('success', 'Status bencana berhasil diselesaikan untuk lahan ' . $land['nama_lahan']);
        }

        return redirect()->to('/disaster')->with('error', 'Gagal menyelesaikan status bencana.');
    }
}