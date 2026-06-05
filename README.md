# 🌾 AgriMapGIS

AgriMapGIS (Agricultural Mapping & Geographic Information System) adalah sistem informasi geografis berbasis web yang dirancang khusus untuk memetakan lahan pertanian, melacak aktivitas petani, dan mengelola mitigasi bencana secara terpusat. 

Sistem ini memfasilitasi kolaborasi interaktif antara **Petani** dan **PPL** (Petugas Penyuluh Lapangan) melalui pemetaan spasial dan validasi geofencing.

---

## 👥 Aktor & Hak Akses (Role)

Sistem ini beroperasi dengan 2 peran utama:

### 1. PPL (Petugas Penyuluh Lapangan)
Bertindak sebagai administrator wilayah yang membina beberapa Kelompok Tani sekaligus.
* **Manajemen Peta:** Mengakses Peta Tematik yang menampilkan status lahan berdasarkan fase tanam, estimasi panen, dan area terdampak bencana.
* **Verifikasi Aktivitas:** Menyetujui atau menolak laporan aktivitas bertani (pemupukan, penanaman, dll) yang diajukan oleh petani.
* **Mitigasi Bencana:** Menandai lahan yang terdampak bencana darurat (Banjir, Hama, Kekeringan) yang otomatis akan mengubah visual peta lahan menjadi merah muda/kuning dan mengirimkan peringatan massal.
* **Komunikasi:** Mengirimkan pesan dan pengumuman kepada petani binaannya.

### 2. Petani
Anggota dari sebuah kelompok tani yang mengelola lahan spesifik.
* **Laporan Aktivitas Geofencing:** Petani dapat melaporkan kegiatan bertani harian. Sistem menggunakan **GPS Geofencing** ketat (batas toleransi 100 meter). Jika petani melaporkan aktivitas dari rumah (di luar area lahan), sistem akan memblokir otomatis data tersebut.
* **Pemantauan Lahan:** Melihat peta poligon lahan milik kelompoknya di atas citra satelit.
* **Pusat Notifikasi:** Menerima notifikasi otomatis untuk peringatan bencana, persetujuan aktivitas, notifikasi panen, dan pesan masuk.

---

## 🌟 Fitur Unggulan

* **Pemetaan Poligon Interaktif (Leaflet.js):** Lahan digambar dalam bentuk poligon GeoJSON akurat di atas *basemap* Google Satellite dan OpenStreetMap.
* **Geofencing Spasial Ketat:** Validasi lokasi aktivitas menggunakan kalkulasi geometris MySQL (`ST_Contains` dan `ST_Distance` yang dikonversi ke skala meter bumi).
* **Smart Thematic Map:** Warna lahan di peta otomatis berubah sesuai dengan data aktivitas terakhir (contoh: Hijau Muda untuk Fase Vegetatif, Kuning untuk Siap Panen).
* **Ngrok Ready:** Konfigurasi *backend* otomatis mendeteksi lalu lintas dari terowongan Ngrok (`X-Forwarded-Host`) dan mengamankan Cookie (Secure/Lax) sehingga sistem dapat langsung di-online-kan ke internet tanpa *error routing*.
* **Manajemen Sesi Otomatis:** Sistem notifikasi terbagi rapi dalam kategori (Sistem, Laporan, Peringatan) dengan algoritma kalkulasi waktu relatif (*time-ago*).

---

## 💻 Teknologi yang Digunakan

* **Backend:** PHP 8.1+ & Framework CodeIgniter 4
* **Database:** MySQL 8.0+ (Menggunakan tipe data Spatial `GEOMETRY` dengan SRID 0)
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla & jQuery), Bootstrap 5
* **Web Mapping:** Leaflet.js
* **Local Server:** Laragon

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Kebutuhan Sistem
Pastikan Anda sudah meng-install **Laragon** (atau XAMPP) dengan PHP >= 8.1 dan MySQL versi 8 ke atas.

### 2. Clone / Unduh Repository
Letakkan folder proyek di dalam direktori root server lokal Anda (misalnya `C:\laragon\www\agrimapgis`).

### 3. Konfigurasi Database
1. Buka phpMyAdmin / HeidiSQL.
2. Buat database baru bernama `agrimapgis`.
3. Import file struktur SQL (*jika ada*) atau jalankan seeder CodeIgniter yang tersedia (`php spark db:seed AgriSeeder`).

### 4. Meng-online-kan Sistem (Ngrok)
Untuk mengakses sistem dari perangkat lain (seperti Handphone) melalui internet:
1. Install Ngrok di komputer Anda.
2. Jalankan perintah terminal:
   ```bash
   ngrok http 80 --host-header=agrimapgis.test
   ```
3. Buka *Forwarding URL* berwarna hijau yang diberikan Ngrok di browser HP/komputer lain. Sistem akan otomatis menyesuaikan konfigurasinya tanpa kendala *login* atau *CSS* yang rusak!

---
*Dokumentasi ini disusun oleh AI Assistant - 2026*
