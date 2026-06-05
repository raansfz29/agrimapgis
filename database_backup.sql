-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: agrimapgis
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id_aktivitas` int NOT NULL AUTO_INCREMENT,
  `id_lahan` int NOT NULL,
  `id_user` int NOT NULL,
  `jenis_aktivitas` varchar(50) NOT NULL,
  `hasil_panen` decimal(10,2) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `koordinat` geometry DEFAULT NULL,
  PRIMARY KEY (`id_aktivitas`),
  KEY `id_lahan` (`id_lahan`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`id_lahan`) REFERENCES `lands` (`id_lahan`) ON DELETE CASCADE,
  CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (37,12,17,'Pemupukan',NULL,NULL,'2026-06-01','Pemupukan susulan NPK pada padi fase vegetatif.','pupuk_lahan1.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0ª¢\─ ░NZ@\÷(\\Å\┬u└'),(38,13,18,'panen',4.50,'Ton','2026-06-03','Panen jagung manis pada area fase generatif matang.','panen_jagung2.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0\∞Q╕àOZ@V-▓¥o└'),(39,14,19,'panen',1.20,'Ton','2026-05-26','Evakuasi sisa tanaman padi akibat luapan banjir 30cm.','evakuasi_banjir.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0ç\┘\╬\≈OZ@ª¢\─ ░r└'),(40,15,20,'Pengolahan Lahan',NULL,NULL,'2026-06-04','Pembajakan tanah menggunakan traktor roda dua.','bajak_lahan4.jpg','pending','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0₧\∩º\╞KOZ@╢\≤²\╘xi└'),(41,16,21,'Penyemprotan Hama',NULL,NULL,'2026-06-02','Penyemprotan pestisida darurat serangan wereng cokelat.','semprot_wereng.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\09┤\╚v╛OZ@F╢\≤²\╘x└'),(42,17,22,'Pemeliharaan',NULL,NULL,'2026-06-02','Pengairan berkala pada lahan jagung fase generatif.',NULL,'approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0\≤\╥o_OZ@\╙\▐\αôi└'),(43,18,23,'panen',3.90,'Ton','2026-05-29','Pembersihan jerami sisa panen raya padi minggu lalu.','pasca_panen7.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0▌╡ä|\╨OZ@~î╣k	y└'),(44,19,24,'Pemupukan',NULL,NULL,'2026-06-03','Pemberian pupuk dasar organik pada padi vegetatif awal.','pupuk_organik8.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0C\δ\Γ6NZ@ì(\φ\r╛p└'),(45,20,25,'Penyemprotan Hama',NULL,NULL,'2026-06-01','Penyemprotan fungisida pencegahan jamur tanaman padi.','semprot_fungi9.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0½>W[▒OZ@▓.nú|└'),(46,21,26,'panen',5.00,'Ton','2026-06-04','Proses pemotongan padi menggunakan mesin combine harvester.','combine_harvester10.jpg','approved','2026-06-04 20:16:08',_binary '\0\0\0\0\0\0\0%üòCOZ@\≈\Σaí\╓t└');
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disaster_logs`
--

DROP TABLE IF EXISTS `disaster_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disaster_logs` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `id_lahan` int NOT NULL,
  `id_user` int NOT NULL,
  `judul_kejadian` varchar(200) NOT NULL,
  `jenis_bencana` enum('banjir','kekeringan','hama','angin_kencang','lainnya') NOT NULL DEFAULT 'banjir',
  `deskripsi_kejadian` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `luas_terdampak` decimal(8,2) DEFAULT NULL,
  `estimasi_kerugian` decimal(15,2) DEFAULT NULL,
  `tindakan_diambil` text,
  `status_penanganan` enum('dalam_penanganan','selesai','butuh_bantuan') NOT NULL DEFAULT 'dalam_penanganan',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `id_lahan` (`id_lahan`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `disaster_logs_ibfk_1` FOREIGN KEY (`id_lahan`) REFERENCES `lands` (`id_lahan`) ON DELETE CASCADE,
  CONSTRAINT `disaster_logs_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disaster_logs`
--

LOCK TABLES `disaster_logs` WRITE;
/*!40000 ALTER TABLE `disaster_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `disaster_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `farmer_groups`
--

DROP TABLE IF EXISTS `farmer_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmer_groups` (
  `id_kelompok` int NOT NULL AUTO_INCREMENT,
  `nama_kelompok` varchar(100) NOT NULL,
  `ketua` varchar(100) NOT NULL,
  `id_ppl` int DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT 'Rajabasa',
  `gapoktan` varchar(100) DEFAULT NULL,
  `komoditas` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_kelompok`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `farmer_groups`
--

LOCK TABLES `farmer_groups` WRITE;
/*!40000 ALTER TABLE `farmer_groups` DISABLE KEYS */;
INSERT INTO `farmer_groups` VALUES (9,'Poktan Mekar Jaya','Bapak Suryanto',1,'Rajabasa','Maju Sejahtera','Padi, Jagung','2026-06-04 19:46:24'),(10,'Poktan Maju Bersama','Bapak Wahyudi',1,'Rajabasa','Maju Sejahtera','Padi, Jagung','2026-06-04 19:46:24'),(11,'Poktan Sukamaju I','Bapak Hartono',1,'Rajabasa','Maju Sejahtera','Padi, Jagung','2026-06-04 19:46:24'),(12,'Poktan Jaya Bersama','Bapak Slamet Riyadi',1,'Rajabasa','Maju Sejahtera','Padi, Jagung','2026-06-04 19:46:25'),(13,'Poktan Tani Mandiri','Bapak Mulyono',1,'Rajabasa','Maju Sejahtera','Padi, Jagung','2026-06-04 19:46:25'),(14,'Poktan Harapan Jaya','Bapak Agus Santoso',1,'Rajabasa','Harapan Makmur','Padi, Jagung','2026-06-04 19:46:25'),(15,'Poktan Sumber Rejeki','Bapak Supriadi',1,'Rajabasa','Harapan Makmur','Padi, Jagung','2026-06-04 19:46:25'),(16,'Poktan Sido Makmur','Ibu Suminah',1,'Rajabasa','Harapan Makmur','Padi','2026-06-04 19:46:25'),(17,'Poktan Karya Tani','Bapak Bambang Eko',1,'Rajabasa','Harapan Makmur','Padi','2026-06-04 19:46:25'),(18,'Poktan Tunas Harapan','Bapak Sudirman',1,'Rajabasa','Harapan Makmur','Padi','2026-06-04 19:46:25');
/*!40000 ALTER TABLE `farmer_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lands`
--

DROP TABLE IF EXISTS `lands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lands` (
  `id_lahan` int NOT NULL AUTO_INCREMENT,
  `id_kelompok` int DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `nama_lahan` varchar(100) DEFAULT NULL,
  `komoditas` enum('padi','jagung') NOT NULL,
  `alamat` text,
  `luas` decimal(10,4) DEFAULT '0.0000',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status_fase` enum('persiapan','tanam','pemeliharaan','panen','bera','darurat') DEFAULT 'persiapan',
  `status_bencana` enum('normal','darurat') DEFAULT 'normal',
  `foto_bencana` varchar(255) DEFAULT NULL,
  `deskripsi_bencana` text,
  `tanggal_bencana` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `geom` geometry NOT NULL,
  PRIMARY KEY (`id_lahan`),
  SPATIAL KEY `geom` (`geom`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lands`
--

LOCK TABLES `lands` WRITE;
/*!40000 ALTER TABLE `lands` DISABLE KEYS */;
INSERT INTO `lands` VALUES (12,9,17,'Sawah Mekar Jaya A','padi','Jl. Kapten Abdul Haq, Rajabasa',22.5000,-5.36420000,105.23450000,'pemeliharaan','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0\σ\╨\"\█∙NZ@\█∙~j╝t└\σ\╨\"\█∙NZ@X9┤\╚v└\╔v╛ƒ\ZOZ@X9┤\╚v└\╔v╛ƒ\ZOZ@\█∙~j╝t└\σ\╨\"\█∙NZ@\█∙~j╝t└'),(13,10,18,'Sawah Maju Bersama','jagung','Jl. Komarudin, Rajabasa',18.2000,-5.35890000,105.24120000,'pemeliharaan','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0üòCïlOZ@;\▀Oìùn└üòCïlOZ@q=\n╫úp└d;\▀OìOZ@q=\n╫úp└d;\▀OìOZ@;\▀Oìùn└üòCïlOZ@;\▀Oìùn└'),(14,11,19,'Ladang Sukamaju Utama','padi','Rajabasa Jaya',31.4000,-5.36110000,105.25040000,'panen','darurat','banjir_lahan3.jpg','Tergenang banjir luapan sungai setinggi 30cm','2026-05-25 00:00:00','2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0PZ@ïl\τ√⌐q└\0\0\0\0\0PZ@\█∙~j╝t└\╒x\Θ&1PZ@\█∙~j╝t└\╒x\Θ&1PZ@ïl\τ√⌐q└\0\0\0\0\0PZ@ïl\τ√⌐q└'),(15,12,20,'Sawah Jaya Bersama','jagung','Rajabasa Raya',15.8000,-5.37100000,105.22890000,'persiapan','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0;\▀OìùNZ@ûCïl\τ{└;\▀OìùNZ@╦íE╢\≤}└à\δQ╕NZ@╦íE╢\≤}└à\δQ╕NZ@ûCïl\τ{└;\▀OìùNZ@ûCïl\τ{└'),(16,13,21,'Sawah Tani Mandiri','padi','Gedong Meneng, Rajabasa',26.3000,-5.37560000,105.23310000,'pemeliharaan','darurat','hama_lahan5.jpg','Terserang hama wereng cokelat skala ringan','2026-06-01 00:00:00','2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0\⌠²\╘x\ΘNZ@\0\0\0\0\0Ç└\⌠²\╘x\ΘNZ@Pìùnâ└╫úp=\nOZ@Pìùnâ└╫úp=\nOZ@\0\0\0\0\0Ç└\⌠²\╘x\ΘNZ@\0\0\0\0\0Ç└'),(17,14,22,'Ladang Harapan Jaya','jagung','Jl. Terusan Haji Mena, Rajabasa',20.1000,-5.35240000,105.23980000,'pemeliharaan','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0₧\∩º\╞KOZ@£\─ ░rh└₧\∩º\╞KOZ@\╤\"\█∙~j└üòCïlOZ@\╤\"\█∙~j└üòCïlOZ@£\─ ░rh└₧\∩º\╞KOZ@£\─ ░rh└'),(18,15,23,'Sawah Sumber Rejeki','padi','Rajabasa Permai',19.5000,-5.36780000,105.24650000,'bera','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\09┤\╚v╛OZ@+ç\┘\╬w└9┤\╚v╛OZ@`\σ\╨\"\█y└Zd;\▀OZ@`\σ\╨\"\█y└Zd;\▀OZ@+ç\┘\╬w└9┤\╚v╛OZ@+ç\┘\╬w└'),(19,16,24,'Sawah Sido Makmur','padi','Jl. Raden Gunawan, Rajabasa',24.6000,-5.35950000,105.22120000,'pemeliharaan','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0á\Z/\▌$NZ@V-▓¥o└á\Z/\▌$NZ@ïl\τ√⌐q└â└╩íENZ@ïl\τ√⌐q└â└╩íENZ@V-▓¥o└á\Z/\▌$NZ@V-▓¥o└'),(20,17,25,'Sawah Karya Tani','padi','Rajabasa Barat',17.8000,-5.37020000,105.24410000,'pemeliharaan','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0V-▓¥OZ@{«G\ßz└V-▓¥OZ@░rhæ\φ|└9┤\╚v╛OZ@░rhæ\φ|└9┤\╚v╛OZ@{«G\ßz└V-▓¥OZ@{«G\ßz└'),(21,18,26,'Sawah Tunas Harapan','padi','Jl. Zainal Abidin Pagar Alam, Rajabasa',22.8000,-5.36330000,105.23780000,'panen','normal',NULL,NULL,NULL,'2026-06-04 12:55:33',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0║I+OZ@┴╩íE╢s└║I+OZ@\÷(\\Å\┬u└₧\∩º\╞KOZ@\÷(\\Å\┬u└₧\∩º\╞KOZ@┴╩íE╢s└║I+OZ@┴╩íE╢s└');
/*!40000 ALTER TABLE `lands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id_pesan` int NOT NULL AUTO_INCREMENT,
  `id_pengirim` int DEFAULT NULL,
  `id_penerima` int DEFAULT NULL,
  `isi_pesan` text,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_pesan`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,2,'halo',1,'2026-05-11 17:00:27'),(2,1,2,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\nΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöü\n≡ƒôì Lokasi   : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu    : 11/05/2026 17:28\nΓÜá∩╕Å  Kejadian : Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\nΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöü\n≡ƒôï TINDAKAN SEGERA:\n1. Segera lakukan drainase dan pompaan air\n2. Proteksi tanaman dengan fungisida pasca banjir\n3. Berikan pupuk daun untuk pemulihan tanaman\n4. Dokumentasikan kerusakan untuk klaim asuransi\nΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöü\nPesan otomatis dari sistem AgriMapGIS oleh Petugas PPL Rajabasa. Hubungi PPL untuk bantuan teknis lebih lanjut.',1,'2026-05-11 17:28:37'),(3,1,3,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\nΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöü\n≡ƒôì Lokasi   : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu    : 11/05/2026 17:28\nΓÜá∩╕Å  Kejadian : Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\nΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöü\n≡ƒôï TINDAKAN SEGERA:\n1. Segera lakukan drainase dan pompaan air\n2. Proteksi tanaman dengan fungisida pasca banjir\n3. Berikan pupuk daun untuk pemulihan tanaman\n4. Dokumentasikan kerusakan untuk klaim asuransi\nΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöüΓöü\nPesan otomatis dari sistem AgriMapGIS oleh Petugas PPL Rajabasa. Hubungi PPL untuk bantuan teknis lebih lanjut.',0,'2026-05-11 17:28:37'),(4,1,2,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu   : 11/05/2026 17:30\nΓÜá∩╕Å Kejadian: Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',1,'2026-05-11 17:30:56'),(5,1,3,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu   : 11/05/2026 17:30\nΓÜá∩╕Å Kejadian: Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-11 17:30:56'),(6,1,2,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu   : 13/05/2026 15:25\nΓÜá∩╕Å Kejadian: Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',1,'2026-05-13 15:25:37'),(7,1,3,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu   : 13/05/2026 15:25\nΓÜá∩╕Å Kejadian: Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-13 15:25:37'),(8,1,2,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu   : 16/05/2026 16:22\nΓÜá∩╕Å Kejadian: Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',1,'2026-05-16 16:22:35'),(9,1,3,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C - Terkena Banjir\n≡ƒòÉ Waktu   : 16/05/2026 16:22\nΓÜá∩╕Å Kejadian: Terjadi banjir akibat luapan sungai setelah hujan deras 3 jam. Tanaman padi usia 90 hari terendam 50cm.\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-16 16:22:35'),(10,1,4,'halo',0,'2026-05-16 16:42:39'),(11,2,1,'halo',1,'2026-05-17 07:14:34'),(12,1,2,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C\n≡ƒòÉ Waktu   : 18/05/2026 08:07\nΓÜá∩╕Å Kejadian: banjir 2 meter\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-18 08:07:47'),(13,1,3,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Sawah Blok C\n≡ƒòÉ Waktu   : 18/05/2026 08:07\nΓÜá∩╕Å Kejadian: banjir 2 meter\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-18 08:07:47'),(14,1,2,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Jagung 1\n≡ƒòÉ Waktu   : 24/05/2026 13:17\nΓÜá∩╕Å Kejadian: Banjir setinggi 2 meter\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-24 13:17:48'),(15,1,3,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Jagung 1\n≡ƒòÉ Waktu   : 24/05/2026 13:17\nΓÜá∩╕Å Kejadian: Banjir setinggi 2 meter\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-24 13:17:48'),(16,1,4,'≡ƒÜ¿ SIAGA BENCANA ΓÇö AgriMapGIS\n------------------------------------\n≡ƒôì Lokasi  : Jagung 2\n≡ƒòÉ Waktu   : 24/05/2026 13:35\nΓÜá∩╕Å Kejadian: Banjir 5 meter\n------------------------------------\n≡ƒôï TINDAKAN SEGERA:\n  1. Lakukan drainase & pompaan air\n  2. Semprotkan fungisida pasca banjir\n  3. Berikan pupuk daun untuk pemulihan\n  4. Dokumentasikan kerusakan\n------------------------------------\nDikirim oleh PPL Petugas PPL Rajabasa via AgriMapGIS.\nHubungi PPL Anda untuk bantuan teknis.',0,'2026-05-24 13:35:10'),(17,1,22,'halo',1,'2026-06-04 17:37:56');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'20260509120000','App\\Database\\Migrations\\CreateAgriTables','default','App',1778365790,1),(2,'20260510140000','App\\Database\\Migrations\\AddDisasterFieldsToLands','default','App',1778405553,2),(3,'20260511000000','App\\Database\\Migrations\\OptimizeSpatialData','default','App',1778502174,3),(4,'2026-05-11-165111','App\\Database\\Migrations\\AddHarvestFieldsToActivities','default','App',1778518297,4),(5,'20260516000000','App\\Database\\Migrations\\FixActivitiesCoordinate','default','App',1778954213,5),(6,'2026-05-16-175700','App\\Database\\Migrations\\AddUserToLands','default','App',1778954231,6),(7,'2026-05-16-181730','App\\Database\\Migrations\\AddLatLongToLands','default','App',1778955461,7),(8,'2026-06-03-114640','App\\Database\\Migrations\\AddIdPplToFarmerGroups','default','App',1780576317,8),(9,'2026-06-03-115646','App\\Database\\Migrations\\AddGapoktanAndKomoditasToFarmerGroups','default','App',1780576317,8),(10,'2026-06-03-142820','App\\Database\\Migrations\\CreateDisasterLogsTable','default','App',1780576317,8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id_notif` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `judul` varchar(100) NOT NULL,
  `pesan` text,
  `tipe` enum('info','warning','danger') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notif`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,2,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-12 00:28:37'),(2,3,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-12 00:28:37'),(3,2,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-12 00:30:56'),(4,3,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-12 00:30:56'),(5,2,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-13 22:25:37'),(6,3,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-13 22:25:37'),(7,2,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-16 23:22:35'),(8,3,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C - Terkena Banjir','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-16 23:22:35'),(9,2,'Γ£à Bencana Selesai: Sawah Blok C - Terkena Banjir','Lahan Sawah Blok C - Terkena Banjir telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','info',0,'2026-05-16 23:30:10'),(10,3,'Γ£à Bencana Selesai: Sawah Blok C - Terkena Banjir','Lahan Sawah Blok C - Terkena Banjir telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','info',0,'2026-05-16 23:30:10'),(11,4,'Pesan Baru dari Petugas PPL Rajabasa','halo','info',0,'2026-05-16 23:42:39'),(12,1,'Pesan Baru dari Budi Santoso','halo','info',1,'2026-05-17 14:14:34'),(13,2,'Γ£à Aktivitas Disetujui PPL','Aktivitas \'Pemupukan NPK\' pada lahan Anda telah disetujui oleh PPL. Terus pertahankan prestasi Anda!','info',0,'2026-05-17 14:31:28'),(14,2,'≡ƒÜ¿ Bencana Baru: Jagung 1','Lahan Jagung 1 kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-17 16:01:16'),(15,3,'≡ƒÜ¿ Bencana Baru: Jagung 1','Lahan Jagung 1 kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-17 16:01:16'),(16,2,'≡ƒÜ¿ Bencana Baru: Sawah Blok C','Lahan Sawah Blok C kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-17 16:02:01'),(17,3,'≡ƒÜ¿ Bencana Baru: Sawah Blok C','Lahan Sawah Blok C kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-17 16:02:01'),(18,2,'Γ£à Bencana Selesai: Jagung 1','Lahan Jagung 1 telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','',0,'2026-05-17 16:02:09'),(19,3,'Γ£à Bencana Selesai: Jagung 1','Lahan Jagung 1 telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','',0,'2026-05-17 16:02:09'),(20,2,'≡ƒÜ¿ Bencana Baru: Sawah Blok C','Lahan Sawah Blok C kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-18 15:07:40'),(21,3,'≡ƒÜ¿ Bencana Baru: Sawah Blok C','Lahan Sawah Blok C kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-18 15:07:40'),(22,2,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-18 15:07:47'),(23,3,'≡ƒÜ¿ Peringatan Darurat: Sawah Blok C','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-18 15:07:47'),(24,1,'≡ƒöö Aktivitas Baru Menunggu Verifikasi','Budi Santoso melaporkan aktivitas \'Pemupukan NPK\' pada lahan Jagung 1. Segera lakukan verifikasi.','warning',1,'2026-05-24 19:48:32'),(25,2,'Γ£à Bencana Selesai: Sawah Blok C','Lahan Sawah Blok C telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','',0,'2026-05-24 20:12:27'),(26,3,'Γ£à Bencana Selesai: Sawah Blok C','Lahan Sawah Blok C telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','',0,'2026-05-24 20:12:27'),(27,2,'≡ƒÜ¿ Bencana Baru: Jagung 1','Lahan Jagung 1 kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-24 20:17:04'),(28,3,'≡ƒÜ¿ Bencana Baru: Jagung 1','Lahan Jagung 1 kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-24 20:17:04'),(29,2,'≡ƒÜ¿ Peringatan Darurat: Jagung 1','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-24 20:17:48'),(30,3,'≡ƒÜ¿ Peringatan Darurat: Jagung 1','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-24 20:17:49'),(31,2,'Γ£à Bencana Selesai: Jagung 1','Lahan Jagung 1 telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','',0,'2026-05-24 20:19:39'),(32,3,'Γ£à Bencana Selesai: Jagung 1','Lahan Jagung 1 telah kembali ke kondisi normal. Lanjutkan aktivitas pertanian dengan hati-hati.','',0,'2026-05-24 20:19:39'),(33,4,'≡ƒÜ¿ Bencana Baru: Jagung 2','Lahan Jagung 2 kini berstatus DARURAT BENCANA. Pantau instruksi mitigasi dari PPL Anda segera.','danger',0,'2026-05-24 20:25:30'),(34,4,'≡ƒÜ¿ Peringatan Darurat: Jagung 2','Terjadi bencana di lahan wilayah Anda. Cek pesan dari PPL Petugas PPL Rajabasa untuk instruksi mitigasi.','danger',0,'2026-05-24 20:35:10'),(35,2,'≡ƒî╛ Waktu Panen: Sawah Blok A','Estimasi masa panen untuk lahan Sawah Blok A (Komoditas: padi) telah tiba. Silakan persiapkan jadwal panen dan periksa kondisi lapangan.','',0,'2026-05-24 21:19:09'),(36,1,'≡ƒî╛ Waktu Panen: Sawah Blok A','Lahan Sawah Blok A di kelompok Kelompok Tani Maju Jaya telah memasuki jadwal panen. Mohon berikan pendampingan kepada petani terkait.','info',1,'2026-05-24 21:19:09'),(38,1,'≡ƒöö Aktivitas Baru Menunggu Verifikasi','Budi Santoso melaporkan aktivitas \'Pemupukan NPK\' pada lahan Jagung 1. Segera lakukan verifikasi.','warning',1,'2026-05-24 21:55:19'),(39,2,'≡ƒî╛ Waktu Panen: Sawah Blok A','Estimasi masa panen untuk lahan Sawah Blok A (Komoditas: padi) telah tiba. Silakan persiapkan jadwal panen dan periksa kondisi lapangan.','',0,'2026-06-02 14:45:56'),(40,1,'≡ƒî╛ Waktu Panen: Sawah Blok A','Lahan Sawah Blok A di kelompok Kelompok Tani Maju Jaya telah memasuki jadwal panen. Mohon berikan pendampingan kepada petani terkait.','info',1,'2026-06-02 14:45:56'),(41,2,'≡ƒî╛ Waktu Panen: Sawah Blok A','Estimasi masa panen untuk lahan Sawah Blok A (Komoditas: padi) telah tiba. Silakan persiapkan jadwal panen dan periksa kondisi lapangan.','',0,'2026-06-04 19:26:03'),(42,1,'≡ƒî╛ Waktu Panen: Sawah Blok A','Lahan Sawah Blok A di kelompok Kelompok Tani Maju Jaya telah memasuki jadwal panen. Mohon berikan pendampingan kepada petani terkait.','info',1,'2026-06-04 19:26:03'),(43,22,'Pesan Baru dari Susetiowati','halo','info',1,'2026-06-05 00:37:56'),(44,1,'≡ƒöö Aktivitas Baru Menunggu Verifikasi','Agus Santoso melaporkan aktivitas \'Pemupukan NPK\' pada lahan Ladang Harapan Jaya. Segera lakukan verifikasi.','warning',0,'2026-06-05 00:48:54'),(45,22,'Γ£à Lokasi Sesuai (Dalam Lahan)','Aktivitas \'Pemupukan NPK\' berhasil dicatat karena lokasi Anda berada di DALAM area lahan. Menunggu verifikasi PPL.','',0,'2026-06-05 00:48:54'),(46,1,'≡ƒöö Aktivitas Baru Menunggu Verifikasi','Agus Santoso melaporkan aktivitas \'Pemupukan NPK\' pada lahan Ladang Harapan Jaya. Segera lakukan verifikasi.','warning',0,'2026-06-05 00:51:22'),(47,22,'Γ£à Lokasi Sesuai (Dalam Lahan)','Aktivitas \'Pemupukan NPK\' berhasil dicatat karena lokasi Anda berada di DALAM area lahan. Menunggu verifikasi PPL.','',0,'2026-06-05 00:51:22'),(48,22,'Γ¥î Aktivitas Ditolak (Luar Lahan)','Upaya input aktivitas \'Pemupukan NPK\' pada Ladang Harapan Jaya ditolak otomatis oleh sistem karena lokasi Anda berada di LUAR jangkauan lahan (jarak: 7574.7 meter). Data tidak disimpan.','danger',0,'2026-06-05 00:53:17');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('petani','ppl','admin') DEFAULT 'petani',
  `id_kelompok` int DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  KEY `id_kelompok` (`id_kelompok`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_kelompok`) REFERENCES `farmer_groups` (`id_kelompok`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Susetiowati','ppl@agrimapgis.test','$2y$10$JyUsRldmL3p9Zqxn562NwO7BvIXza5Th5isMLoeUGD/BrAYWyD/bi','ppl',NULL,'081234567890','2026-05-10 09:13:58'),(17,'Suryanto','suryanto@agrimapgis.test','$2y$10$egpVkBli5.ymSl2.5jybCOwrcWniBkMi/og/0IufkHeVog6AJFp.W','petani',9,NULL,'2026-06-04 19:46:24'),(18,'Wahyudi','wahyudi@agrimapgis.test','$2y$10$m/QEfvLhgDmqOpXnj4056uh.NnNtVSu2sIBusQd3jkRLzUpmoPaMm','petani',10,NULL,'2026-06-04 19:46:24'),(19,'Hartono','hartono@agrimapgis.test','$2y$10$o1FFRv1rNQe.M9lGjqnDFe3a.cCjdM.w4/ZT5cOssQyTrRT2r.NSS','petani',11,NULL,'2026-06-04 19:46:25'),(20,'Slamet Riyadi','slametriyadi@agrimapgis.test','$2y$10$ztBhIg49UTzYEns/P3z6bOqEOO0u1kF27Y6y22eX/Y1tXDw0t2bGO','petani',12,NULL,'2026-06-04 19:46:25'),(21,'Mulyono','mulyono@agrimapgis.test','$2y$10$MmqAvjB3p/qTwkR.0VLzsOV/6gg2pxh6Njyf4d0LWNkr27XifS/V6','petani',13,NULL,'2026-06-04 19:46:25'),(22,'Agus Santoso','agussantoso@agrimapgis.test','$2y$10$3IKqznrDu5UuaEukEDvH3OHsTljABpDDqBlzd7j9SkXD3rBjY/umS','petani',14,'08123456789','2026-06-04 19:46:25'),(23,'Supriadi','supriadi@agrimapgis.test','$2y$10$6rVqDsal2Mz0BmiM5dIM9.6Oh59mR5ukW8ZFvVHSS0EZuXs9QtHsO','petani',15,NULL,'2026-06-04 19:46:25'),(24,'Suminah','suminah@agrimapgis.test','$2y$10$eB0tAVbxL3u05zwpTwq/0ehm79VTUALYDI/OT2GhUg4cfWUaoo.3S','petani',16,NULL,'2026-06-04 19:46:25'),(25,'Bambang Eko','bambangeko@agrimapgis.test','$2y$10$QGV7oSdKIZn8rQUCSaPdUeM7QwyBAkLvyOmRqzdohx5OfazYTcla6','petani',17,NULL,'2026-06-04 19:46:25'),(26,'Sudirman','sudirman@agrimapgis.test','$2y$10$ubQr.p8kVOFMLmk3UVKfg.vl8ROdZz6yKjHLb8w17epg11AegCbx6','petani',18,NULL,'2026-06-04 19:46:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-05 18:02:55
