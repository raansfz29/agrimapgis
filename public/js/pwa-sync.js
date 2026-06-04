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
 * Save activity to offline storage
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
 * Synchronize offline activities with the server
 */
async function syncActivities() {
    if (!navigator.onLine) return { success: false, message: "Anda masih offline." };

    const offlineData = await db.offline_activities.where("synced").equals(0).toArray();
    if (offlineData.length === 0) return { success: true, message: "Semua data sudah sinkron." };

    let successCount = 0;
    for (const item of offlineData) {
        try {
            const formData = new FormData();
            Object.keys(item).forEach(key => {
                if (key !== 'id' && key !== 'synced') {
                    formData.append(key, item[key]);
                }
            });

            const response = await fetch('/activity/save', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                await db.offline_activities.update(item.id, { synced: 1 });
                successCount++;
            }
        } catch (error) {
            console.error("Sync Error for item", item.id, error);
        }
    }

    return { 
        success: true, 
        message: `${successCount} aktivitas berhasil disinkronkan.`,
        count: successCount 
    };
}

// Auto-sync when coming back online
window.addEventListener('online', async () => {
    console.log("Koneksi kembali. Memulai sinkronisasi...");
    const result = await syncActivities();
    if (result.count > 0) {
        alert(result.message);
    }
});
