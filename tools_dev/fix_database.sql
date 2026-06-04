-- Manual SQL Script to Fix AgriMapGIS Database
-- Run this in MySQL if migrations don't work

-- Drop tables if they exist
DROP TABLE IF EXISTS activities;
DROP TABLE IF EXISTS lands;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS farmer_groups;

-- Create farmer_groups table
CREATE TABLE farmer_groups (
    id_kelompok INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelompok VARCHAR(100) NOT NULL,
    ketua VARCHAR(100) NOT NULL,
    kecamatan VARCHAR(50) DEFAULT 'Rajabasa',
    created_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create users table
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('petani', 'ppl', 'admin') DEFAULT 'petani',
    id_kelompok INT,
    telepon VARCHAR(20),
    created_at DATETIME,
    FOREIGN KEY (id_kelompok) REFERENCES farmer_groups(id_kelompok) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create lands table
CREATE TABLE lands (
    id_lahan INT AUTO_INCREMENT PRIMARY KEY,
    id_kelompok INT,
    nama_lahan VARCHAR(100),
    komoditas ENUM('padi', 'jagung') NOT NULL,
    geom LONGTEXT,
    luas DECIMAL(10,4) DEFAULT 0,
    status_fase ENUM('persiapan','tanam','pemeliharaan','panen','bera','darurat') DEFAULT 'persiapan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create activities table (MOST IMPORTANT)
CREATE TABLE activities (
    id_aktivitas INT AUTO_INCREMENT PRIMARY KEY,
    id_lahan INT NOT NULL,
    id_user INT NOT NULL,
    jenis_aktivitas VARCHAR(50) NOT NULL,
    tanggal DATE NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255),
    koordinat VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_lahan) REFERENCES lands(id_lahan) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Verify tables were created
SHOW TABLES;
DESCRIBE activities;
