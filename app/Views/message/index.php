<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<style>
    .contact-item {
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e2e8f0 !important;
    }
    .contact-item:hover {
        background-color: #f0fdf4 !important;
        border-color: #1e7e34 !important;
    }
    .contact-item.active {
        background-color: #f0fdf4 !important;
        border-color: #1e7e34 !important;
        box-shadow: 0 0 0 2px rgba(30,126,52,0.15);
    }
    .chat-panel {
        height: calc(100vh - 200px);
        min-height: 500px;
        display: flex;
        flex-direction: column;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: white;
    }
    .chat-messages-panel {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .msg-bubble {
        max-width: 80%;
        padding: 12px 18px;
        border-radius: 18px;
        font-size: 13.5px;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        line-height: 1.7;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .msg-sent {
        background: #1e7e34;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }
    .msg-received {
        background: white;
        color: #1e293b;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
        border: 1px solid #e2e8f0;
    }
    .msg-time {
        display: block;
        font-size: 10px;
        margin-top: 6px;
        opacity: 0.6;
        text-align: right;
    }
    .chat-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        height: 100%;
        padding: 40px;
        color: #94a3b8;
    }
    .contact-list-panel {
        height: calc(100vh - 200px);
        min-height: 500px;
        overflow-y: auto;
    }
    #chat-loading {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #94a3b8;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-3 fw-bold"><i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-3 fw-bold"><i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Daftar Kontak -->
    <div class="col-md-4">
        <div class="premium-card p-0 overflow-hidden" style="height: calc(100vh - 200px); min-height: 500px; display: flex; flex-direction: column;">
            <div class="p-4 border-bottom">
                <h6 class="fw-800 mb-0">Kontak Binaan</h6>
            </div>
            <div class="contact-list-panel p-3">
                <?php if (empty($contacts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fs-1 text-muted opacity-25 mb-3"></i>
                        <p class="text-muted small">Tidak ada kontak yang terhubung.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                        <div class="contact-item d-flex align-items-center gap-3 p-3 rounded-3 mb-2 text-dark"
                             data-id="<?= $contact['id_user'] ?>"
                             data-nama="<?= esc($contact['nama']) ?>"
                             onclick="loadChat(<?= $contact['id_user'] ?>, '<?= esc($contact['nama']) ?>')">
                            <div class="bg-primary bg-opacity-10 text-primary fw-800 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                <?= strtoupper(substr($contact['nama'], 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0" style="font-size: 14px;"><?= esc($contact['nama']) ?></h6>
                                <span class="text-muted small"><?= esc(ucfirst($contact['role'])) ?></span>
                            </div>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Panel Chat -->
    <div class="col-md-8">
        <div class="chat-panel shadow-sm" id="chatPanel">

            <!-- Empty State -->
            <div class="chat-empty-state" id="chatEmptyState">
                <i class="fas fa-comments fs-1 mb-4 opacity-25"></i>
                <h5 class="fw-800">Mulai Konsultasi</h5>
                <p class="small px-4">Pilih petani dari daftar di sebelah kiri untuk memulai diskusi atau memberikan bimbingan teknis.</p>
            </div>

            <!-- Loading State -->
            <div id="chat-loading">
                <i class="fas fa-spinner fa-spin fs-3 mb-3"></i>
                <p class="small">Memuat percakapan...</p>
            </div>

            <!-- Chat UI (hidden until contact selected) -->
            <div id="chatUI" style="display:none; flex-direction:column; height:100%;">
                <!-- Header -->
                <div class="chat-header p-3 px-4 border-bottom bg-white d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary fw-800 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" id="chatAvatar">?</div>
                    <div>
                        <h6 class="fw-800 mb-0" id="chatName">-</h6>
                        <span class="text-success small fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i>Online</span>
                    </div>
                </div>

                <!-- Messages -->
                <div class="chat-messages-panel" id="chatBox"></div>

                <!-- Input -->
                <div class="p-3 px-4 border-top bg-white">
                    <form id="chatForm" class="d-flex gap-2">
                        <?= csrf_field() ?>
                        <input type="hidden" id="targetId" value="">
                        <input type="text" id="messageInput" class="form-control border-0 bg-light rounded-pill px-4" placeholder="Ketik pesan konsultasi di sini..." autocomplete="off">
                        <button type="submit" class="btn btn-success rounded-circle" style="width: 45px; height: 45px;"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let currentTargetId = null;
    let refreshInterval = null;
    const baseUrl = '<?= base_url() ?>';

    // Get CSRF token from the hidden input in the form
    function getCsrfToken() {
        const input = document.querySelector('#chatForm input[name="csrf_test_name"]');
        return input ? input.value : '';
    }

    function getCsrfFieldName() {
        const input = document.querySelector('#chatForm input[type="hidden"]:not(#targetId)');
        return input ? input.name : 'csrf_test_name';
    }

    function loadChat(userId, userName) {
        document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
        const clickedItem = document.querySelector(`.contact-item[data-id="${userId}"]`);
        if (clickedItem) clickedItem.classList.add('active');

        document.getElementById('chatEmptyState').style.display = 'none';
        document.getElementById('chatUI').style.display = 'none';
        document.getElementById('chat-loading').style.display = 'flex';

        currentTargetId = userId;
        document.getElementById('chatName').textContent = userName;
        document.getElementById('chatAvatar').textContent = userName.charAt(0).toUpperCase();
        document.getElementById('targetId').value = userId;

        fetchMessages(userId, true);

        if (refreshInterval) clearInterval(refreshInterval);
        refreshInterval = setInterval(() => {
            if (currentTargetId) fetchMessages(currentTargetId, false);
        }, 5000);
    }

    function fetchMessages(userId, initialLoad) {
        fetch(`${baseUrl}message/messages/${userId}`)
            .then(res => res.json())
            .then(data => {
                const chatBox = document.getElementById('chatBox');
                const myId = <?= session()->get('id_user') ?>;

                let html = '';
                if (data.length === 0) {
                    html = '<div class="text-center py-5 text-muted small opacity-50"><p>Belum ada percakapan. Mulai obrolan untuk konsultasi.</p></div>';
                } else {
                    data.forEach(msg => {
                        const isSent = msg.id_pengirim == myId;
                        const isDisaster = msg.isi_pesan.includes('SIAGA BENCANA');
                        let cls = isSent ? 'msg-sent' : 'msg-received';
                        if (isDisaster) cls += isSent ? ' msg-disaster-sent' : ' msg-disaster-received';
                        const time = msg.created_at ? msg.created_at.substr(11, 5) : '';
                        const text = msg.isi_pesan
                            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                            .replace(/\n/g,'<br>');
                        html += `<div class="msg-bubble ${cls}">${text}<span class="msg-time">${time}</span></div>`;
                    });
                }

                const shouldScroll = initialLoad || chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 80;
                chatBox.innerHTML = html;
                if (shouldScroll) chatBox.scrollTop = chatBox.scrollHeight;

                document.getElementById('chat-loading').style.display = 'none';
                const chatUI = document.getElementById('chatUI');
                chatUI.style.display = 'flex';
            })
            .catch(() => {
                document.getElementById('chat-loading').style.display = 'none';
                document.getElementById('chatEmptyState').style.display = 'flex';
            });
    }

    document.getElementById('chatForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        if (!message || !currentTargetId) return;

        messageInput.value = '';

        // Optimistic bubble
        const chatBox = document.getElementById('chatBox');
        const placeholder = chatBox.querySelector('.text-center.py-5');
        if (placeholder) placeholder.remove();

        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const text = message.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
        chatBox.insertAdjacentHTML('beforeend', `<div class="msg-bubble msg-sent">${text}<span class="msg-time">${time}</span></div>`);
        chatBox.scrollTop = chatBox.scrollHeight;

        // Send via AJAX with CSRF token
        try {
            const formData = new FormData();
            formData.append('id_penerima', currentTargetId);
            formData.append('isi_pesan', message);
            formData.append(getCsrfFieldName(), getCsrfToken());

            const response = await fetch(`${baseUrl}message/send-ajax`, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                console.error('Gagal kirim pesan, status:', response.status);
            } else {
                // Update CSRF token from response header if provided
                const newToken = response.headers.get('X-CSRF-TOKEN');
                if (newToken) {
                    const inp = document.querySelector('#chatForm input[name="csrf_test_name"]');
                    if (inp) inp.value = newToken;
                }
                // Refresh messages after successful send
                setTimeout(() => fetchMessages(currentTargetId, false), 300);
            }
        } catch (err) {
            console.error('Error:', err);
        }
    });
</script>
<?= $this->endSection() ?>
