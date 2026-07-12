/**
 * AgriMapGIS PWA Synchronization & Offline Storage
 * Uses Dexie.js for IndexedDB management.
 */

// Initialize Dexie
const db = new Dexie("AgriMapDatabase");
db.version(1).stores({
    offline_activities: "++id, id_lahan, jenis_aktivitas, tanggal, deskripsi, latitude, longitude, foto, synced"
});

/**
 * Get CSRF token from the page's meta tag or hidden input
 */
function getCsrfData() {
    // Try meta tag first (most reliable)
    const metaName  = document.querySelector('meta[name="csrf-token-name"]');
    const metaHash  = document.querySelector('meta[name="csrf-token-value"]');
    if (metaName && metaHash) {
        return { name: metaName.getAttribute('content'), hash: metaHash.getAttribute('content') };
    }
    // Fallback: any hidden CSRF input anywhere on the page
    const hiddenInput = document.querySelector('input[name^="csrf"]');
    if (hiddenInput) {
        return { name: hiddenInput.name, hash: hiddenInput.value };
    }
    return null;
}

/**
 * Save activity to offline storage (IndexedDB)
 */
async function saveActivityOffline(data) {
    try {
        await db.offline_activities.add({
            ...data,
            synced: 0,
            created_at: new Date().toISOString()
        });
        return true;
    } catch (error) {
        console.error("Dexie Error:", error);
        return false;
    }
}

/**
 * Synchronize offline activities with the server via the dedicated JSON endpoint.
 */
async function syncActivities() {
    if (!navigator.onLine) return { success: false, message: "Anda masih offline. Coba lagi saat koneksi tersedia." };

    const offlineData = await db.offline_activities.where("synced").equals(0).toArray();
    if (offlineData.length === 0) return { success: true, message: "Semua data sudah sinkron dengan server.", count: 0 };

    let successCount = 0;
    let failCount    = 0;
    const errors     = [];

    for (const item of offlineData) {
        try {
            const csrf = getCsrfData();

            const formData = new FormData();
            // Send all stored fields except internal ones
            Object.keys(item).forEach(key => {
                if (key !== 'id' && key !== 'synced' && item[key] !== undefined && item[key] !== null) {
                    formData.append(key, item[key]);
                }
            });

            // Attach CSRF token so CodeIgniter accepts the request
            if (csrf) {
                formData.append(csrf.name, csrf.hash);
            }

            const response = await fetch('/activity/sync-offline', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'  // Send session cookie
            });

            // Parse JSON response from the dedicated endpoint
            let json = {};
            try {
                json = await response.json();
            } catch (e) {
                // Response was not JSON (e.g. redirect to login page)
                json = { success: false, message: 'Server mengembalikan respons tidak valid. Pastikan Anda masih login.' };
            }

            if (json.success) {
                await db.offline_activities.update(item.id, { synced: 1 });
                successCount++;
            } else {
                failCount++;
                errors.push(json.message || 'Gagal tidak diketahui');
                console.warn("Sync failed for item", item.id, json.message);
            }
        } catch (error) {
            failCount++;
            errors.push(error.message);
            console.error("Sync Error for item", item.id, error);
        }
    }

    const total = offlineData.length;
    let message = `${successCount} dari ${total} aktivitas berhasil disinkronkan.`;
    if (failCount > 0) {
        message += ` ${failCount} gagal: ${errors.slice(0, 2).join('; ')}`;
    }

    return {
        success: failCount === 0,
        message: message,
        count: successCount
    };
}

// Auto-sync when coming back online
window.addEventListener('online', async () => {
    console.log("Koneksi kembali. Memulai sinkronisasi otomatis...");
    const result = await syncActivities();
    if (result.count > 0 || !result.success) {
        alert("📡 Sinkronisasi Otomatis:\n" + result.message);
    }
    // Refresh sync status indicator if function exists
    if (typeof updateSyncStatus === 'function') {
        updateSyncStatus();
    }
});
