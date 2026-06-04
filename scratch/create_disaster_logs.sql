-- Run this SQL in your MySQL database (agrimapgis)
-- Create disaster_logs table

CREATE TABLE IF NOT EXISTS `disaster_logs` (
    `id_log`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_lahan`            INT UNSIGNED NOT NULL,
    `id_user`             INT UNSIGNED NOT NULL,
    `judul_kejadian`      VARCHAR(200)  NOT NULL,
    `jenis_bencana`       ENUM('banjir','kekeringan','hama','angin_kencang','lainnya') NOT NULL DEFAULT 'banjir',
    `deskripsi_kejadian`  TEXT          NOT NULL,
    `luas_terdampak`      DECIMAL(8,2)  NULL COMMENT 'Dalam hektar',
    `estimasi_kerugian`   DECIMAL(15,2) NULL COMMENT 'Dalam rupiah',
    `tindakan_diambil`    TEXT          NULL,
    `status_penanganan`   ENUM('dalam_penanganan','selesai','butuh_bantuan') NOT NULL DEFAULT 'dalam_penanganan',
    `created_at`          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_log`),
    FOREIGN KEY (`id_lahan`) REFERENCES `lands`(`id_lahan`) ON DELETE CASCADE,
    FOREIGN KEY (`id_user`)  REFERENCES `users`(`id_user`)  ON DELETE CASCADE,
    INDEX (`id_lahan`),
    INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
