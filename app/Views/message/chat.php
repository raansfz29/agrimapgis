<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<style>
    .chat-container-fixed {
        height: calc(100vh - 150px);
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    .chat-messages {
        flex-grow: 1;
        padding: 25px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .msg-bubble {
        max-width: 80%;
        padding: 14px 20px;
        border-radius: 18px;
        font-size: 13.5px;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        line-height: 1.7;
        white-space: pre-wrap;  /* Preserve \n as line breaks */
        word-break: break-word;
    }
    .msg-sent {
        background: var(--primary-green);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }
    .msg-received {
        background: white;
        color: var(--text-dark);
        align-self: flex-start;
        border-bottom-left-radius: 4px;
        border: 1px solid var(--border-color);
    }
    /* Special styling for disaster broadcast bubbles */
    .msg-bubble.msg-disaster-sent {
        background: linear-gradient(135deg, #155724, #1e7e34);
        max-width: 88%;
        border-left: 4px solid #4ade80;
        border-bottom-right-radius: 4px;
        font-size: 13px;
    }
    .msg-bubble.msg-disaster-received {
        background: #fff1f2;
        border-left: 4px solid #ef4444;
        color: var(--text-dark);
        max-width: 88%;
        border-bottom-left-radius: 4px;
        border: 1px solid #fecdd3;
    }
    .msg-time {
        display: block;
        font-size: 10px;
        margin-top: 8px;
        opacity: 0.6;
        text-align: right;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="chat-container-fixed shadow-sm">
    <div class="chat-header p-3 px-4 border-bottom bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('message') ?>" class="btn btn-light btn-sm rounded-circle"><i class="fas fa-arrow-left"></i></a>
            <div class="bg-primary bg-opacity-10 text-primary fw-800 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <?= strtoupper(substr($target['nama'], 0, 1)) ?>
            </div>
            <div>
                <h6 class="fw-800 mb-0"><?= esc($target['nama']) ?></h6>
                <span class="text-success small fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Online</span>
            </div>
        </div>
    </div>

    <div class="chat-messages" id="chatBox">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5 text-muted small opacity-50">
                <p>Belum ada percakapan. Mulai obrolan untuk konsultasi.</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <?php
                    $isSent     = $msg['id_pengirim'] == session()->get('id_user');
                    $isDisaster = strpos($msg['isi_pesan'], 'SIAGA BENCANA') !== false;
                    $bubbleClass = $isSent
                        ? ($isDisaster ? 'msg-sent msg-disaster-sent' : 'msg-sent')
                        : ($isDisaster ? 'msg-received msg-disaster-received' : 'msg-received');
                ?>
                <div class="msg-bubble <?= $bubbleClass ?>"><?= nl2br(esc($msg['isi_pesan'])) ?><span class="msg-time"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="chat-footer p-3 px-4 border-top bg-white">
        <form id="chatForm" class="d-flex gap-2">
            <?= csrf_field() ?>
            <input type="hidden" name="id_penerima" id="targetId" value="<?= $target['id_user'] ?>">
            <input type="text" name="isi_pesan" id="messageInput" class="form-control border-0 bg-light rounded-pill px-4" placeholder="Ketik pesan konsultasi di sini..." required autocomplete="off">
            <button type="submit" id="sendBtn" class="btn btn-success rounded-circle" style="width: 45px; height: 45px;"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const chatBox = document.getElementById("chatBox");
    const chatForm = document.getElementById("chatForm");
    const messageInput = document.getElementById("messageInput");
    
    chatBox.scrollTop = chatBox.scrollHeight;

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        const targetId = document.getElementById("targetId").value;
        const csrfToken = document.querySelector('input[name="csrf_test_name"]').value;

        // Optimistically add message to UI
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const bubble = `
            <div class="msg-bubble msg-sent">
                ${message}
                <span class="msg-time">${time}</span>
            </div>
        `;
        
        const placeholder = document.querySelector('.text-center.py-5');
        if (placeholder) placeholder.remove();
        
        chatBox.insertAdjacentHTML('beforeend', bubble);
        chatBox.scrollTop = chatBox.scrollHeight;
        messageInput.value = '';

        try {
            const formData = new FormData();
            formData.append('id_penerima', targetId);
            formData.append('isi_pesan', message);
            formData.append('csrf_test_name', csrfToken);

            const response = await fetch('<?= base_url('message/send') ?>', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                alert('Gagal mengirim pesan. Silakan coba lagi.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan jaringan.');
        }
    });

    // Auto-refresh messages every 5 seconds
    setInterval(async () => {
        try {
            const response = await fetch('<?= current_url() ?>');
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newMessages = doc.getElementById('chatBox').innerHTML;
            
            // Only update if changed (simple check)
            if (chatBox.innerHTML !== newMessages) {
                chatBox.innerHTML = newMessages;
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        } catch (e) {}
    }, 5000);
</script>
<?= $this->endSection() ?>
