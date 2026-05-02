-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: srv1362.hstgr.io    Database: u971422264_bumil
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-log

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
-- Table structure for table `anaks`
--

DROP TABLE IF EXISTS `anaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anaks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ibu_id` bigint(20) unsigned NOT NULL,
  `kehamilan_id` bigint(20) unsigned DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `anak_ke` int(11) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('bayi','balita','anak','tidak_aktif') DEFAULT 'bayi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nik` (`nik`),
  UNIQUE KEY `kehamilan_id` (`kehamilan_id`),
  KEY `anaks_ibu_id_foreign` (`ibu_id`),
  CONSTRAINT `anaks_ibu_id_foreign` FOREIGN KEY (`ibu_id`) REFERENCES `ibus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `anaks_kehamilan_id_foreign` FOREIGN KEY (`kehamilan_id`) REFERENCES `kehamilans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anaks`
--

LOCK TABLES `anaks` WRITE;
/*!40000 ALTER TABLE `anaks` DISABLE KEYS */;
INSERT INTO `anaks` VALUES (1,1,NULL,'1234432111112222','Anak Try','2025-09-08','P',1,'Kediri','bayi','2026-03-31 04:44:12','2026-04-12 12:20:18',NULL),(2,1,NULL,NULL,'Muhammad Amin R','2025-07-03','L',NULL,'garagata rt 3','bayi','2026-04-12 15:13:33','2026-04-12 15:13:33',NULL),(3,3,NULL,NULL,'nadiva al maira','2023-12-28','P',NULL,'purui rt 01','bayi','2026-04-14 02:10:22','2026-04-14 02:10:22',NULL),(4,2,NULL,NULL,'Muhammad Efri Rayansyah','2024-10-04','L',NULL,'purui rt 2','bayi','2026-04-14 02:38:42','2026-04-14 02:38:42',NULL),(5,1,NULL,NULL,'Muhammad Iqbal','2025-07-21','L',NULL,'Lano rt 5','bayi','2026-04-16 01:38:50','2026-04-16 01:38:50',NULL),(6,2,NULL,NULL,'Kirana','2025-02-05','P',NULL,'teratau rt 5','bayi','2026-04-17 01:30:00','2026-04-17 01:30:00',NULL),(7,2,NULL,NULL,'nur khalisa salsabila','2024-09-17','P',NULL,'teratau rt 1','bayi','2026-04-17 01:40:40','2026-04-17 01:40:40',NULL),(8,2,NULL,NULL,'salzea fitriani','2025-01-04','P',NULL,'teratau rt 5','bayi','2026-04-17 01:43:39','2026-04-17 01:43:39',NULL),(9,2,NULL,NULL,'m. abizar al fatih','2025-05-08','L',NULL,'terarau rt 1','bayi','2026-04-17 01:46:03','2026-04-17 01:46:03',NULL),(10,2,NULL,NULL,'sintiya nor alia','2025-03-07','P',NULL,'teratau rt 4','bayi','2026-04-17 01:49:57','2026-04-17 01:49:57',NULL),(11,2,NULL,NULL,'milka chanissa thiana dhevi thanvika','2025-03-14','P',NULL,'teratau rt 01','bayi','2026-04-17 02:05:50','2026-04-17 02:05:50',NULL),(12,2,NULL,NULL,'norhanifa putri devina','2025-05-07','P',NULL,'teratau rt 4','bayi','2026-04-17 03:04:59','2026-04-17 03:04:59',NULL),(18,1,NULL,'1234432144445555','Anakku','2025-12-27','L',4,'Kediri','bayi','2026-04-18 14:03:00','2026-04-18 14:19:17',NULL);
/*!40000 ALTER TABLE `anaks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asi_eksklusifs`
--

DROP TABLE IF EXISTS `asi_eksklusifs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asi_eksklusifs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anak_id` bigint(20) unsigned NOT NULL,
  `bulan_ke` int(11) NOT NULL,
  `status_asi` tinyint(1) NOT NULL DEFAULT 1,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asi_eksklusifs_anak_id_bulan_ke_unique` (`anak_id`,`bulan_ke`),
  CONSTRAINT `asi_eksklusifs_anak_id_foreign` FOREIGN KEY (`anak_id`) REFERENCES `anaks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asi_eksklusifs`
--

LOCK TABLES `asi_eksklusifs` WRITE;
/*!40000 ALTER TABLE `asi_eksklusifs` DISABLE KEYS */;
INSERT INTO `asi_eksklusifs` VALUES (1,1,1,1,NULL,'2026-04-16 11:26:23','2026-04-16 12:47:49'),(2,1,2,1,NULL,'2026-04-16 12:50:12','2026-04-16 12:50:12');
/*!40000 ALTER TABLE `asi_eksklusifs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ibus`
--

DROP TABLE IF EXISTS `ibus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('calon_ibu','hamil','menyusui','tidak_aktif') DEFAULT 'calon_ibu',
  `pendidikan` varchar(255) DEFAULT NULL,
  `pekerjaan` varchar(255) DEFAULT NULL,
  `no_hp` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibus_created_by_foreign` (`created_by`),
  KEY `fk_ibus_updated_by` (`updated_by`),
  KEY `ibus_user_id_foreign` (`user_id`),
  CONSTRAINT `fk_ibus_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ibus_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ibus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ibus`
--

LOCK TABLES `ibus` WRITE;
/*!40000 ALTER TABLE `ibus` DISABLE KEYS */;
INSERT INTO `ibus` VALUES (1,'1234567899998888','Ibu Try','2000-01-13','Kediri','hamil','SMA','Ibu Rumah Tangga','6287656554321',NULL,2,'2026-03-31 04:19:42','2026-04-20 12:45:39',NULL,5),(2,'0000111122223333','Ibu Kedua',NULL,'Kediri','calon_ibu',NULL,NULL,'6287656554322',NULL,NULL,'2026-04-02 14:16:25','2026-04-02 14:16:25',NULL,NULL),(3,'6309116604090001','Kayla Apriliani',NULL,'garagata rt 8','calon_ibu',NULL,NULL,'6282353086133',NULL,NULL,'2026-04-12 15:05:12','2026-04-12 15:05:12',NULL,NULL),(4,'0000787865653434','Ibuku','2001-01-05','Kediri','calon_ibu','SMA','Ibu Rumah Tangga','6289766565433',2,2,'2026-04-17 04:20:15','2026-04-17 14:07:06',NULL,NULL),(5,'1234432166667777','Ibu Example','2000-01-28','Kediri','calon_ibu',NULL,NULL,'087654432221',7,NULL,'2026-04-22 10:35:51','2026-04-22 10:35:51',NULL,NULL);
/*!40000 ALTER TABLE `ibus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kehamilans`
--

DROP TABLE IF EXISTS `kehamilans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kehamilans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ibu_id` bigint(20) unsigned NOT NULL,
  `hpht` date DEFAULT NULL,
  `status` enum('hamil','selesai','gugur') NOT NULL DEFAULT 'hamil',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ibu_status` (`ibu_id`,`status`),
  CONSTRAINT `kehamilans_ibu_id_foreign` FOREIGN KEY (`ibu_id`) REFERENCES `ibus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kehamilans`
--

LOCK TABLES `kehamilans` WRITE;
/*!40000 ALTER TABLE `kehamilans` DISABLE KEYS */;
INSERT INTO `kehamilans` VALUES (1,2,'2026-01-17','hamil','2026-04-02 14:18:05','2026-04-03 03:58:27'),(2,3,'2025-08-20','hamil','2026-04-12 15:07:43','2026-04-12 15:07:43'),(6,1,'2026-04-07','hamil','2026-04-19 14:40:18','2026-04-20 12:45:39');
/*!40000 ALTER TABLE `kehamilans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konseling_rules`
--

DROP TABLE IF EXISTS `konseling_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konseling_rules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kategori` varchar(50) DEFAULT NULL,
  `rule_group` varchar(255) NOT NULL,
  `logic_group` varchar(255) DEFAULT NULL,
  `parameter` varchar(255) NOT NULL,
  `operator` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `data_type` enum('number','boolean','string') NOT NULL DEFAULT 'number',
  `isi_konseling` text NOT NULL,
  `output_type` enum('konseling','warning','score','action') DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 1,
  `score` int(11) NOT NULL DEFAULT 0,
  `is_risk` tinyint(1) NOT NULL DEFAULT 0,
  `label` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `logic_operator` enum('AND','OR') DEFAULT 'AND',
  PRIMARY KEY (`id`),
  KEY `konseling_rules_kategori_index` (`kategori`),
  KEY `konseling_rules_parameter_index` (`parameter`),
  KEY `konseling_rules_logic_group_index` (`logic_group`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konseling_rules`
--

LOCK TABLES `konseling_rules` WRITE;
/*!40000 ALTER TABLE `konseling_rules` DISABLE KEYS */;
INSERT INTO `konseling_rules` VALUES (1,'ttd','kepatuhan','A','kepatuhan_persen','<','50','number','Kepatuhan minum TTD rendah','warning',8,3,1,'ttd_rendah',NULL,NULL,'AND'),(2,'ttd','kepatuhan','B','kepatuhan_persen','<','30','number','Kepatuhan sangat rendah, perlu edukasi intensif','warning',9,4,1,'ttd_sangat_rendah',NULL,NULL,'AND'),(3,'ttd','kepatuhan','C','kepatuhan_persen','>=','50','number','Kepatuhan cukup, perlu ditingkatkan','konseling',5,2,0,'ttd_cukup',NULL,NULL,'AND'),(4,'ttd','kepatuhan','C','kepatuhan_persen','<','80','number','Kepatuhan cukup, perlu ditingkatkan','konseling',5,2,0,'ttd_cukup',NULL,NULL,'AND'),(5,'ttd','kepatuhan','D','kepatuhan_persen','>=','80','number','Kepatuhan sangat baik, pertahankan','konseling',3,1,0,'ttd_baik',NULL,NULL,'AND'),(6,'ttd','kepatuhan','E','kepatuhan_persen','>=','90','number','Kepatuhan optimal, sangat baik','konseling',2,1,0,'ttd_sangat_baik',NULL,NULL,'AND'),(7,'ttd','anemia','A','total_tidak','>=','7','number','Risiko anemia sangat tinggi, segera rujuk','action',10,6,1,'anemia_urgent',NULL,NULL,'AND'),(8,'ttd','anemia','B','total_tidak','>=','5','number','Risiko anemia tinggi, perlu konsultasi','warning',9,5,1,'anemia_tinggi',NULL,NULL,'AND'),(9,'ttd','anemia','C','total_tidak','>','3','number','Risiko anemia mulai meningkat','warning',7,3,1,'anemia_sedang',NULL,NULL,'AND'),(10,'ttd','mingguan','A','jumlah_minum','<','2','number','Frekuensi minum minggu ini sangat rendah','warning',7,3,1,'ttd_mingguan_buruk',NULL,NULL,'AND'),(11,'ttd','mingguan','B','jumlah_minum','>=','6','number','Konsumsi minggu ini sangat baik','konseling',3,1,0,'ttd_mingguan_baik',NULL,NULL,'AND'),(12,'ttd','edukasi','A','total_patuh','<','3','number','Minum TTD rutin membantu mencegah anemia','konseling',4,1,0,'edukasi_ttd',NULL,NULL,'AND'),(13,'ttd','bulan_terakhir','A','bulan_6','=','0','number','TTD bulan terakhir belum diminum','warning',6,2,1,'ttd_bulan_terakhir',NULL,NULL,'AND'),(14,'ttd','kombinasi','A','kepatuhan_persen','<','50','number','Risiko tinggi karena kepatuhan rendah dan perilaku buruk','warning',9,5,1,'kombinasi_risiko',NULL,NULL,'AND'),(15,'ttd','kombinasi','A','total_tidak','>=','5','number','Risiko tinggi karena kepatuhan rendah dan perilaku buruk','warning',9,5,1,'kombinasi_risiko',NULL,NULL,'AND'),(16,'mpasi','usia','A','usia_bulan','<','6','number','MPASI sebaiknya dimulai usia 6 bulan','warning',10,5,1,'mpasi_dini',NULL,NULL,'AND'),(17,'mpasi','mdd','A','mdd_score','<','4','number','Keragaman makanan sangat kurang','warning',8,4,1,'mdd_sangat_kurang',NULL,NULL,'AND'),(18,'mpasi','mdd','B','mdd_score','>=','4','number','Keragaman cukup','konseling',5,2,0,'mdd_cukup',NULL,NULL,'AND'),(19,'mpasi','mdd','B','mdd_score','<','5','number','Keragaman cukup','konseling',5,2,0,'mdd_cukup',NULL,NULL,'AND'),(20,'mpasi','frekuensi','A','frekuensi_makan','<','2','number','Frekuensi makan sangat kurang','warning',8,4,1,'mmf_sangat_kurang',NULL,NULL,'AND'),(21,'mpasi','frekuensi','B','frekuensi_makan','>=','2','number','Frekuensi cukup','konseling',5,2,0,'mmf_cukup',NULL,NULL,'AND'),(22,'mpasi','frekuensi','B','frekuensi_makan','<','3','number','Frekuensi cukup','konseling',5,2,0,'mmf_cukup',NULL,NULL,'AND'),(23,'mpasi','mad','A','mad_status','=','0','number','Minimum Acceptable Diet belum tercapai','warning',9,5,1,'mad_gagal',NULL,NULL,'AND'),(24,'mpasi','protein','A','protein_hewani','=','0','number','Tambahkan protein hewani','konseling',5,2,0,'protein_hewani_kurang',NULL,NULL,'AND'),(25,'mpasi','protein','B','protein_nabati','=','0','number','Tambahkan protein nabati','konseling',4,1,0,'protein_nabati_kurang',NULL,NULL,'AND'),(26,'mpasi','vitamin','A','sayur','=','0','number','Tambahkan sayur','konseling',4,1,0,'sayur_kurang',NULL,NULL,'AND'),(27,'mpasi','vitamin','B','buah','=','0','number','Tambahkan buah','konseling',4,1,0,'buah_kurang',NULL,NULL,'AND'),(28,'mpasi','energi','A','karbohidrat','=','0','number','Karbohidrat penting sebagai energi','konseling',3,1,0,'karbo_kurang',NULL,NULL,'AND'),(29,'mpasi','asi','A','asi','=','0','number','ASI tetap penting diberikan','konseling',4,1,0,'asi_tidak',NULL,NULL,'AND'),(30,'asi','eksklusif','A','status_asi','=','0','number','ASI eksklusif belum tercapai','warning',8,4,1,'asi_eks_gagal',NULL,NULL,'AND'),(31,'asi','eksklusif','B','status_asi','=','1','number','Pertahankan ASI eksklusif','konseling',2,1,0,'asi_baik',NULL,NULL,'AND'),(32,'asi','usia','A','usia_bulan','<','6','number','ASI eksklusif dianjurkan','konseling',5,2,0,'asi_eks_edukasi',NULL,NULL,'AND'),(33,'asi','lanjutan','A','usia_bulan','<=','24','number','ASI tetap diberikan hingga 2 tahun','konseling',3,1,0,'asi_lanjut',NULL,NULL,'AND'),(34,'asi','frekuensi','A','frekuensi_asi','<','8','number','Frekuensi menyusui kurang','konseling',4,2,0,'asi_kurang',NULL,NULL,'AND'),(35,'asi','risiko','A','status_asi','=','0','number','Risiko gizi meningkat tanpa ASI','warning',8,5,1,'risiko_tanpa_asi',NULL,NULL,'AND'),(36,'asi','rujukan','A','status_asi','=','0','number','Segera konsultasi ke tenaga kesehatan','action',10,6,1,'rujukan_asi',NULL,NULL,'AND'),(37,'ttd_remaja','mingguan','A','jumlah_minum','=','0','number','Tidak minum TTD minggu ini','warning',9,4,1,'remaja_tidak_minum',NULL,NULL,'AND'),(38,'ttd_remaja','mingguan','B','jumlah_minum','>=','1','number','Konsumsi belum rutin, perlu ditingkatkan','konseling',5,2,0,'remaja_kurang',NULL,NULL,'AND'),(39,'ttd_remaja','mingguan','B','jumlah_minum','<','4','number','Konsumsi belum rutin, perlu ditingkatkan','konseling',5,2,0,'remaja_kurang',NULL,NULL,'AND'),(40,'ttd_remaja','mingguan','C','jumlah_minum','>=','4','number','Konsumsi sudah baik, pertahankan','konseling',3,1,0,'remaja_baik',NULL,NULL,'AND'),(41,'ttd_remaja','kepatuhan','A','kepatuhan_persen','<','30','number','Kepatuhan sangat rendah, perlu edukasi intensif','warning',9,5,1,'remaja_sangat_rendah',NULL,NULL,'AND'),(42,'ttd_remaja','kepatuhan','B','kepatuhan_persen','>=','30','number','Kepatuhan rendah, perlu pendampingan','warning',8,4,1,'remaja_rendah',NULL,NULL,'AND'),(43,'ttd_remaja','kepatuhan','B','kepatuhan_persen','<','50','number','Kepatuhan rendah, perlu pendampingan','warning',8,4,1,'remaja_rendah',NULL,NULL,'AND'),(44,'ttd_remaja','kepatuhan','C','kepatuhan_persen','>=','50','number','Kepatuhan cukup, perlu ditingkatkan','konseling',5,2,0,'remaja_cukup',NULL,NULL,'AND'),(45,'ttd_remaja','kepatuhan','C','kepatuhan_persen','<','80','number','Kepatuhan cukup, perlu ditingkatkan','konseling',5,2,0,'remaja_cukup',NULL,NULL,'AND'),(46,'ttd_remaja','kepatuhan','D','kepatuhan_persen','>=','80','number','Kepatuhan baik, pertahankan','konseling',3,1,0,'remaja_baik_histori',NULL,NULL,'AND'),(47,'ttd_remaja','perilaku','A','total_tidak','>=','5','number','Sering tidak minum TTD, risiko anemia meningkat','warning',8,4,1,'remaja_risiko',NULL,NULL,'AND'),(48,'ttd_remaja','perilaku','B','total_tidak','>=','8','number','Risiko anemia tinggi, perlu intervensi serius','action',10,6,1,'remaja_urgent',NULL,NULL,'AND'),(49,'ttd_remaja','kombinasi','A','kepatuhan_persen','<','50','number','Risiko tinggi karena kepatuhan rendah dan perilaku buruk','warning',9,5,1,'kombinasi_risiko',NULL,NULL,'AND'),(50,'ttd_remaja','kombinasi','A','total_tidak','>=','5','number','Risiko tinggi karena kepatuhan rendah dan perilaku buruk','warning',9,5,1,'kombinasi_risiko',NULL,NULL,'AND');
/*!40000 ALTER TABLE `konseling_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konselings`
--

DROP TABLE IF EXISTS `konselings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konselings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) DEFAULT NULL,
  `min_minggu` int(10) unsigned DEFAULT NULL,
  `max_minggu` int(10) unsigned DEFAULT NULL,
  `min_bulan` int(10) unsigned DEFAULT NULL,
  `max_bulan` int(10) unsigned DEFAULT NULL,
  `materi` text NOT NULL,
  `kategori` enum('kehamilan','anak') NOT NULL DEFAULT 'kehamilan',
  `resiko` enum('normal','tinggi') NOT NULL DEFAULT 'normal',
  `priority` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `konselings_kategori_resiko_index` (`kategori`,`resiko`),
  KEY `konselings_min_minggu_max_minggu_index` (`min_minggu`,`max_minggu`),
  KEY `konselings_min_bulan_max_bulan_index` (`min_bulan`,`max_bulan`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konselings`
--

LOCK TABLES `konselings` WRITE;
/*!40000 ALTER TABLE `konselings` DISABLE KEYS */;
INSERT INTO `konselings` VALUES (1,'Trimester 1 - Nutrisi',0,12,NULL,NULL,'Konsumsi asam folat untuk mencegah cacat janin','kehamilan','normal',2,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(2,'Trimester 1 - Istirahat',0,12,NULL,NULL,'Istirahat cukup dan hindari aktivitas berat','kehamilan','normal',1,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(3,'Trimester 2 - Gizi',13,27,NULL,NULL,'Perbanyak konsumsi protein, zat besi, dan kalsium','kehamilan','normal',2,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(4,'Trimester 2 - Aktivitas',13,27,NULL,NULL,'Lakukan aktivitas ringan seperti jalan pagi','kehamilan','normal',1,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(5,'Trimester 3 - Persalinan',28,40,NULL,NULL,'Persiapkan persalinan dan kenali tanda bahaya','kehamilan','normal',3,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(6,'Trimester 3 - Kontrol',28,40,NULL,NULL,'Periksa kehamilan minimal 2 minggu sekali','kehamilan','normal',2,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(7,'Risiko Tinggi - Umum',NULL,NULL,NULL,NULL,'Segera rujuk ke fasilitas kesehatan','kehamilan','tinggi',5,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(8,'Risiko - Anemia',NULL,NULL,NULL,NULL,'Tingkatkan konsumsi TTD dan makanan tinggi zat besi','kehamilan','tinggi',5,'2026-04-05 13:26:23','2026-04-05 13:26:23'),(9,'Risiko - Kepatuhan Rendah',NULL,NULL,NULL,NULL,'Edukasi pentingnya konsumsi TTD setiap hari','kehamilan','tinggi',4,'2026-04-05 13:26:23','2026-04-05 13:26:23');
/*!40000 ALTER TABLE `konselings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_18_124209_add_role_to_users_table',1),(5,'2026_03_18_124747_create_konseling_rules_table',1),(6,'2026_03_18_124748_create_ibus_table',1),(7,'2026_03_18_124809_create_remaja_putris_table',1),(8,'2026_03_18_124837_create_ttd_remajas_table',1),(9,'2026_03_28_021151_create_personal_access_tokens_table',1),(10,'2026_03_30_132406_create_kehamilans_table',1),(11,'2026_03_30_133058_create_anaks_table',1),(12,'2026_03_30_133236_create_asi_eksklusifs_table',1),(13,'2026_03_30_133306_create_pmbas_table',1),(14,'2026_03_30_133337_create_pmba_details_table',1),(15,'2026_03_30_142549_create_ttd_ibus_table',1),(16,'2026_04_01_143127_create_pertumbuhan_anaks_table',2),(17,'2026_04_03_102643_create_monitorings_table',3),(18,'2026_04_03_103753_create_konselings_table',3),(19,'2026_04_04_125417_add_ttd_mmj_to_monitorings_table',4),(20,'2026_04_04_130447_drop_tanggal_ttd_mmj_from_monitorings_table',5),(21,'2026_04_04_132002_add_info_to_konseling_rules_table',6),(22,'2026_04_05_015758_create_konseling_rules_table',7),(23,'2026_04_05_020126_create_konselings_table',7),(24,'2026_04_05_021536_create_monitorings_table',7),(25,'2026_04_05_135535_create_konseling_rules_table',8),(26,'2026_04_07_114138_change_hasil_konseling_to_json',9),(27,'2026_04_08_122421_add_tipe_to_pmbas_table',10),(28,'2026_04_08_122652_add_fields_to_pmba_details_table',10),(29,'2026_04_08_123109_add_fields_to_asis_table',11),(30,'2026_04_08_123730_add_fields_to_pmba_details_table',12),(31,'2026_04_08_134257_alter_konseling_rules_table',13),(32,'2026_04_09_025654_update_kategori_enum_in_konseling_rules_table',14),(33,'2026_04_14_040442_add_fields_to_remaja_putris_table',14),(34,'2026_04_14_041222_create_remaja_putris_table',15),(35,'2026_04_14_041316_create_ttd_remajas_table',15),(36,'2026_04_18_024325_update_role_enum_on_users_table',16),(37,'2026_04_18_135258_add_user_id_to_ibus',17);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monitorings`
--

DROP TABLE IF EXISTS `monitorings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitorings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kehamilan_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `bulan_1` tinyint(1) NOT NULL DEFAULT 0,
  `bulan_2` tinyint(1) NOT NULL DEFAULT 0,
  `bulan_3` tinyint(1) NOT NULL DEFAULT 0,
  `bulan_4` tinyint(1) NOT NULL DEFAULT 0,
  `bulan_5` tinyint(1) NOT NULL DEFAULT 0,
  `bulan_6` tinyint(1) NOT NULL DEFAULT 0,
  `total_patum` int(11) NOT NULL DEFAULT 0,
  `status_kepatuhan` enum('baik','cukup','kurang') DEFAULT NULL,
  `is_risk` tinyint(1) NOT NULL DEFAULT 0,
  `hasil_konseling` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hasil_konseling`)),
  `catatan_kader` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_tidak` int(11) DEFAULT 0,
  `kepatuhan_persen` int(11) DEFAULT 0,
  `bulan_aktif` int(11) DEFAULT 0,
  `total_patuh` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `monitorings_kehamilan_id_foreign` (`kehamilan_id`),
  CONSTRAINT `monitorings_kehamilan_id_foreign` FOREIGN KEY (`kehamilan_id`) REFERENCES `kehamilans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitorings`
--

LOCK TABLES `monitorings` WRITE;
/*!40000 ALTER TABLE `monitorings` DISABLE KEYS */;
INSERT INTO `monitorings` VALUES (2,1,'2026-04-07',1,0,0,0,0,0,0,'kurang',1,'[\"Kepatuhan sangat rendah (<50%)\",\"Kepatuhan cukup, masih perlu ditingkatkan\",\"Hampir tidak pernah minum TTD\",\"Konsumsi TTD masih kurang\",\"Bulan terakhir tidak minum TTD, sangat berisiko\",\"Sering tidak minum TTD, perlu pendampingan kader\"]',NULL,'2026-04-07 13:31:11','2026-04-07 13:31:11',0,0,0,1),(5,2,'2026-04-12',1,1,1,1,1,0,0,'baik',1,'[\"TTD bulan terakhir belum diminum\",\"Trimester akhir membutuhkan zat besi lebih tinggi\",\"Persiapkan persalinan dan kenali tanda bahaya\",\"Periksa kehamilan minimal 2 minggu sekali\"]',NULL,'2026-04-12 15:09:02','2026-04-12 15:09:02',0,0,0,5),(6,6,'2026-04-20',1,1,0,0,0,0,0,'kurang',1,'[\"Kepatuhan minum TTD rendah\",\"Risiko anemia mulai meningkat\",\"TTD bulan terakhir belum diminum\",\"Minum TTD rutin membantu mencegah anemia\",\"Segera rujuk ke fasilitas kesehatan\",\"Tingkatkan konsumsi TTD dan makanan tinggi zat besi\",\"Edukasi pentingnya konsumsi TTD setiap hari\",\"Konsumsi asam folat untuk mencegah cacat janin\",\"Istirahat cukup dan hindari aktivitas berat\"]',NULL,'2026-04-20 13:23:06','2026-04-21 12:39:05',0,0,0,2);
/*!40000 ALTER TABLE `monitorings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',2,'api-token','78d8e1bf7597f4f0ccf4f4ddfb4c54d786694acc4baaa833c69853fa9112a8c9','[\"*\"]','2026-03-30 14:40:56',NULL,'2026-03-30 14:32:14','2026-03-30 14:40:56'),(2,'App\\Models\\User',2,'api-token','543a2ac97e8ad358a41c455360fc46f4a9155ed0826105f2fcacc9a54ebec58d','[\"*\"]','2026-03-30 14:46:11',NULL,'2026-03-30 14:41:15','2026-03-30 14:46:11'),(3,'App\\Models\\User',2,'api-token','46f02cafff869154e9df18e3cc16394d5abd2c00fae388f7078b20e6ee63509d','[\"*\"]','2026-04-18 13:46:27',NULL,'2026-03-30 14:46:42','2026-04-18 13:46:27'),(4,'App\\Models\\User',2,'api-token','cb02daffb52d84edcda41612961152a036ccc78213774d4d267d8c3d60333238','[\"*\"]','2026-03-30 14:51:54',NULL,'2026-03-30 14:51:46','2026-03-30 14:51:54'),(5,'App\\Models\\User',2,'api-token','c4b84b70af177e8bb41fd17cd44c2da0ce94148dfd892cfc3c105410607acd3a','[\"*\"]','2026-03-31 04:49:56',NULL,'2026-03-31 03:34:56','2026-03-31 04:49:56'),(6,'App\\Models\\User',2,'api-token','340d97cbc4c45f19e3e3fdebebf477a21ddd2cbb60e9be2e5cf6767a3d842704','[\"*\"]','2026-03-31 04:58:36',NULL,'2026-03-31 04:57:40','2026-03-31 04:58:36'),(7,'App\\Models\\User',2,'api-token','c306fbdde67876f75a7cf316aee4a6587a5863c518eeb3ed40d533f884b5600b','[\"*\"]','2026-03-31 13:46:33',NULL,'2026-03-31 12:36:19','2026-03-31 13:46:33'),(8,'App\\Models\\User',2,'api-token','6f14d4a37397c84eec8dced0b6875ed2ccaffe59b774d1f1dc553a2d414e713b','[\"*\"]','2026-03-31 13:50:21',NULL,'2026-03-31 13:50:08','2026-03-31 13:50:21'),(9,'App\\Models\\User',2,'api-token','094ccfea2de14557368e1bb58fd57c2abce80872c79feb4573742c8162a0366d','[\"*\"]','2026-03-31 13:59:33',NULL,'2026-03-31 13:58:59','2026-03-31 13:59:33'),(10,'App\\Models\\User',2,'api-token','9ddbaf25be80cca42cbb2cc326e9ba246485567628403fa5a2d62aee0b705c39','[\"*\"]','2026-03-31 14:15:59',NULL,'2026-03-31 14:13:21','2026-03-31 14:15:59'),(11,'App\\Models\\User',2,'api-token','ed44e21ca426ae7b3c9081892659bbc664f66dc3b3e473b28cbef8e206152c99','[\"*\"]','2026-03-31 14:20:33',NULL,'2026-03-31 14:20:13','2026-03-31 14:20:33'),(12,'App\\Models\\User',2,'api-token','2f681b7c2f4f2cc3f452f56ec8d04fa6ded6af8d7ba4281893141f5e8efa1624','[\"*\"]','2026-03-31 14:30:03',NULL,'2026-03-31 14:24:48','2026-03-31 14:30:03'),(13,'App\\Models\\User',2,'api-token','5f874a11d8f7c3257342cc46bc9b265832c4984be7356539ca86b9f11e60655e','[\"*\"]','2026-03-31 14:38:28',NULL,'2026-03-31 14:37:36','2026-03-31 14:38:28'),(14,'App\\Models\\User',2,'api-token','afc33b272c8126b17975ec3b7c4a75b43321bd98a64905c2c738bbd5f06230c9','[\"*\"]','2026-03-31 14:59:03',NULL,'2026-03-31 14:45:19','2026-03-31 14:59:03'),(15,'App\\Models\\User',2,'api-token','f92d4e8606bbeba376c2ab8aa90cd9284ba0ad54ff3c536f24e6370169f00a6e','[\"*\"]','2026-04-01 03:59:35',NULL,'2026-04-01 02:49:43','2026-04-01 03:59:35'),(16,'App\\Models\\User',2,'api-token','0e82291e15cd3f5cb718883d45739ec3fff0d60884d069c4b887d93dab5f0d7b','[\"*\"]','2026-04-01 04:25:18',NULL,'2026-04-01 04:13:22','2026-04-01 04:25:18'),(17,'App\\Models\\User',2,'api-token','58a7efabb873e0bfe7fcfc746537cd055238bc744261ef1c569566c0ab1692e6','[\"*\"]','2026-04-01 04:43:50',NULL,'2026-04-01 04:28:15','2026-04-01 04:43:50'),(18,'App\\Models\\User',2,'api-token','48951bc2dd363097d8b9723ea7edbeb2ba9ff2a32d69ac97c64ee79568b90f21','[\"*\"]','2026-04-01 12:40:56',NULL,'2026-04-01 11:44:35','2026-04-01 12:40:56'),(19,'App\\Models\\User',2,'api-token','34ec9f74602dc985153068d452f280dc1db1bf72f480b1b8e702c6310a16f405','[\"*\"]','2026-04-01 12:57:27',NULL,'2026-04-01 12:50:23','2026-04-01 12:57:27'),(20,'App\\Models\\User',2,'api-token','211f81311665d5fd4265d7fb35e87540b7942c9f11ee04f6178198b9b1e6bff7','[\"*\"]','2026-04-01 14:55:30',NULL,'2026-04-01 14:33:31','2026-04-01 14:55:30'),(21,'App\\Models\\User',2,'api-token','87a8de935d4af0f9ef0bae674b48c5dc8e55193c40f6d946e5d758767b585a5c','[\"*\"]','2026-04-02 02:34:08',NULL,'2026-04-02 01:48:48','2026-04-02 02:34:08'),(22,'App\\Models\\User',2,'api-token','0931fb46b3db52a21c23b35a27db6dc568a665a35006a0802f1d486c13448fe0','[\"*\"]','2026-04-02 03:56:16',NULL,'2026-04-02 03:25:50','2026-04-02 03:56:16'),(23,'App\\Models\\User',2,'api-token','d2887e8b06a81e01c9ab1da69112fc7c9b1c46f77dd6341443848aa7a60dc1c9','[\"*\"]','2026-04-02 04:42:56',NULL,'2026-04-02 03:59:09','2026-04-02 04:42:56'),(24,'App\\Models\\User',1,'api-token','bbe62f2b9ab238f49ae318bfdb55f86aa10caec7187fdc07f9edc1dfd316ce7e','[\"*\"]','2026-04-02 11:48:20',NULL,'2026-04-02 11:48:04','2026-04-02 11:48:20'),(25,'App\\Models\\User',2,'api-token','6bf3661cd47b53c0e38021d471ec0bd40a1bd0e8e94ffb14cf147f131c11722f','[\"*\"]','2026-04-02 13:22:53',NULL,'2026-04-02 12:04:31','2026-04-02 13:22:53'),(26,'App\\Models\\User',2,'api-token','072f62c2691d868e1ce5e207b4c294c261325520439847ad4bfb7f4110af6852','[\"*\"]','2026-04-02 13:43:06',NULL,'2026-04-02 13:25:34','2026-04-02 13:43:06'),(27,'App\\Models\\User',2,'api-token','387b11b82c137ddb494740c4095acdb53811f4d5474b4bd8b9d671a81f0d2c99','[\"*\"]','2026-04-02 14:24:23',NULL,'2026-04-02 13:47:11','2026-04-02 14:24:23'),(28,'App\\Models\\User',2,'api-token','fd926726b7091ed7473dadfa5ae69aa49998dc05241499fada857dc637393314','[\"*\"]','2026-04-02 14:46:05',NULL,'2026-04-02 14:27:13','2026-04-02 14:46:05'),(29,'App\\Models\\User',2,'api-token','b15ed2d0a30c22c6092db1857225b19ca6f9236ff7377a084cbaaa03c04fb78f','[\"*\"]','2026-04-03 04:22:02',NULL,'2026-04-03 02:32:18','2026-04-03 04:22:02'),(30,'App\\Models\\User',2,'api-token','666e2b96305187304820426b02bfe6fcf05f232a01765fcc268a3900793b999d','[\"*\"]','2026-04-03 10:06:47',NULL,'2026-04-03 10:01:35','2026-04-03 10:06:47'),(31,'App\\Models\\User',2,'api-token','6fd7483e60dfb434f4d9af021e91cf7a7fcc29bbe644fab84ec23fe823eaa4d7','[\"*\"]','2026-04-03 13:49:38',NULL,'2026-04-03 13:03:25','2026-04-03 13:49:38'),(32,'App\\Models\\User',2,'api-token','deed573e6e05cb0857b7917285b375590b27ed13ff0763984c38e61a1417e4fb','[\"*\"]','2026-04-03 14:03:03',NULL,'2026-04-03 13:53:12','2026-04-03 14:03:03'),(33,'App\\Models\\User',2,'api-token','f4a0c3b637bb36de5964bb057e18e6001e2b312090eec1f119e2bd1e990afafa','[\"*\"]','2026-04-03 15:18:30',NULL,'2026-04-03 14:09:18','2026-04-03 15:18:30'),(34,'App\\Models\\User',2,'api-token','94ff0ca279708bdd1efee2da1838d937bec1361a84440dfa5ab2f61f69eed50b','[\"*\"]','2026-04-04 10:41:00',NULL,'2026-04-04 10:39:33','2026-04-04 10:41:00'),(35,'App\\Models\\User',2,'api-token','2be4bf12fb4394d6c7518fc37e5d193165ec1a76a29d98f6d191c5369127bd9c','[\"*\"]','2026-04-04 12:12:04',NULL,'2026-04-04 11:49:29','2026-04-04 12:12:04'),(36,'App\\Models\\User',2,'api-token','b19e5705c367e2e36fcb9b0342dbaab95d1ab5b7c19d726e1c79ffe9b2293091','[\"*\"]','2026-04-04 13:40:37',NULL,'2026-04-04 13:34:13','2026-04-04 13:40:37'),(37,'App\\Models\\User',2,'api-token','c4c880b37eaa0ce32e1bf3e2a535f0813782c9f9a074299d07882874267a6cba','[\"*\"]','2026-04-04 15:12:46',NULL,'2026-04-04 14:11:11','2026-04-04 15:12:46'),(38,'App\\Models\\User',2,'api-token','48f6666bd9ed4caad544720bec971ebed95e5acc1b4af39c88861592663f40d3','[\"*\"]','2026-04-05 01:39:22',NULL,'2026-04-05 01:33:55','2026-04-05 01:39:22'),(39,'App\\Models\\User',2,'api-token','3f768a2028cc269e528dfdeee5350ef3df1606b59e4a39b18fd127ed11cbae3d','[\"*\"]','2026-04-05 13:50:17',NULL,'2026-04-05 12:32:07','2026-04-05 13:50:17'),(40,'App\\Models\\User',2,'api-token','fed4208f31839bf2f956f09d8d2ee5c12ddd4d4bd55ae47e9d4808fbbc5df86d','[\"*\"]','2026-04-05 14:53:23',NULL,'2026-04-05 14:24:55','2026-04-05 14:53:23'),(41,'App\\Models\\User',2,'api-token','453ff25e9725f9057da24750f8e7fddca1920891f1eb06e6f672a729811f3c72','[\"*\"]','2026-04-06 02:51:21',NULL,'2026-04-06 02:24:21','2026-04-06 02:51:21'),(42,'App\\Models\\User',2,'api-token','82ab5c32398932654e23c0033099281d2c77bff1b243c7dbdc53b94420f43b48','[\"*\"]','2026-04-06 03:01:32',NULL,'2026-04-06 02:55:10','2026-04-06 03:01:32'),(43,'App\\Models\\User',2,'api-token','1044d6d2b8deb13a03403fc7d057762eb49749e675e90ea657cfd7d7bc7d855d','[\"*\"]','2026-04-06 03:10:11',NULL,'2026-04-06 03:04:58','2026-04-06 03:10:11'),(44,'App\\Models\\User',2,'api-token','95683b6e82bf3fb541af9b6fb0387c5429c2bb527d64eb99ffd601b0de130e4b','[\"*\"]','2026-04-06 04:14:43',NULL,'2026-04-06 04:14:29','2026-04-06 04:14:43'),(45,'App\\Models\\User',2,'api-token','63877c0118ea499362dfe0eb5dd94c1209c26e18a39b2711c0a0ff80e2ecd8e7','[\"*\"]','2026-04-07 10:38:05',NULL,'2026-04-07 10:04:08','2026-04-07 10:38:05'),(46,'App\\Models\\User',2,'api-token','7733735b8cd6921135e50c8077dc265fce01c78ace0c3e867856ff88a5f9b85a','[\"*\"]','2026-04-07 11:26:37',NULL,'2026-04-07 10:44:25','2026-04-07 11:26:37'),(47,'App\\Models\\User',2,'api-token','f92494a797cd787623ddaefae1c664f91ffd99ae54d03b01d2d601fca875b917','[\"*\"]','2026-04-07 11:52:50',NULL,'2026-04-07 11:52:36','2026-04-07 11:52:50'),(48,'App\\Models\\User',2,'api-token','739cc196cef440bc88ae27b2b06fe399daaa394881b0d9af94531910c8b236f0','[\"*\"]','2026-04-07 12:23:24',NULL,'2026-04-07 12:11:22','2026-04-07 12:23:24'),(49,'App\\Models\\User',2,'api-token','7e6ac3f26e65e21f4d67ea2bb74e6ab5b5bdb307d3ee4d80d4f770f07490dc4e','[\"*\"]','2026-04-07 14:11:15',NULL,'2026-04-07 13:03:26','2026-04-07 14:11:15'),(50,'App\\Models\\User',2,'api-token','b4b724bc72597fe19846e1a758366e7021588dd193cf1dc9b08d3d8065aea949','[\"*\"]','2026-04-07 14:33:20',NULL,'2026-04-07 14:14:07','2026-04-07 14:33:20'),(51,'App\\Models\\User',2,'api-token','10e49cd26b88ab7badf7bf32c11baf9862b745f1c680fd181797d969818514e0','[\"*\"]','2026-04-08 04:21:23',NULL,'2026-04-08 02:51:09','2026-04-08 04:21:23'),(52,'App\\Models\\User',2,'api-token','3c45f61003e0b289d6ee593aaf390e8527959a647c664a98763a5a27b53ecece','[\"*\"]','2026-04-08 10:38:16',NULL,'2026-04-08 10:33:31','2026-04-08 10:38:16'),(53,'App\\Models\\User',2,'api-token','27f3d92046747874ecf578e26710b5466c0427d59e8e8febfaed48d58faee453','[\"*\"]','2026-04-08 13:41:33',NULL,'2026-04-08 10:43:36','2026-04-08 13:41:33'),(54,'App\\Models\\User',2,'api-token','81898628da9fb0b75893c328edf8f7b572a00c5f57476ef5830b9f4dd41b7f3b','[\"*\"]','2026-04-09 04:31:29',NULL,'2026-04-09 04:25:18','2026-04-09 04:31:29'),(55,'App\\Models\\User',2,'api-token','91b81a8bcdec697b791483bda26d62bfce9a52baf79b0cb555217a6495fdc15b','[\"*\"]','2026-04-09 05:02:33',NULL,'2026-04-09 04:34:59','2026-04-09 05:02:33'),(56,'App\\Models\\User',2,'api-token','d90b7d93d8f313c3bd7fac71a361d07e7ddf7cc0881adea24cc73c1c8d6e2261','[\"*\"]','2026-04-09 11:16:15',NULL,'2026-04-09 10:35:58','2026-04-09 11:16:15'),(57,'App\\Models\\User',2,'api-token','3db9252aededd62a20bd4adcba2f0a18f2d24c68096efd231985cbd9f012c35d','[\"*\"]','2026-04-09 14:32:52',NULL,'2026-04-09 13:14:10','2026-04-09 14:32:52'),(58,'App\\Models\\User',2,'api-token','7bbf007b6012cf19eaef2957c7b640f1e93bd3185b8f1df64f0dc78b3f3a1914','[\"*\"]','2026-04-10 05:04:52',NULL,'2026-04-10 04:25:25','2026-04-10 05:04:52'),(59,'App\\Models\\User',2,'api-token','9b36059389cc0f211f2a77bb594bed2a67e9cf43c9424538750a904362734d0b','[\"*\"]','2026-04-10 10:08:22',NULL,'2026-04-10 10:01:20','2026-04-10 10:08:22'),(60,'App\\Models\\User',2,'api-token','e5610d7a4641b81e8a28647cc38d29e017d72b417c75186fdc08f9bcdd8d000b','[\"*\"]','2026-04-10 10:49:11',NULL,'2026-04-10 10:14:22','2026-04-10 10:49:11'),(61,'App\\Models\\User',2,'api-token','5bda9864e4c5efe1a7627ec79485b20f09b6912a0852c3ab7e699422986faa1c','[\"*\"]','2026-04-10 12:29:10',NULL,'2026-04-10 12:10:15','2026-04-10 12:29:10'),(62,'App\\Models\\User',2,'api-token','def144fd192acb9b8acdb2e3fb225ccbd62d707a7ac23a97c0cb33a5400429f1','[\"*\"]','2026-04-10 12:33:31',NULL,'2026-04-10 12:33:00','2026-04-10 12:33:31'),(63,'App\\Models\\User',2,'api-token','c2cd6f203f4224510c79d1918ed0941fb46c0dee282e991a79fb952040e70bb8','[\"*\"]','2026-04-10 13:47:54',NULL,'2026-04-10 13:06:07','2026-04-10 13:47:54'),(64,'App\\Models\\User',2,'api-token','5649da96d4413d8b0fa2794044fce7792810ce1e344eb77b3cfaf0c201974fcb','[\"*\"]','2026-04-10 15:06:43',NULL,'2026-04-10 13:51:49','2026-04-10 15:06:43'),(65,'App\\Models\\User',2,'api-token','ea33b2278a0d15b8f4b9a9511c456128870624d8eb14e72859d032d7202fdb6a','[\"*\"]','2026-04-11 03:15:04',NULL,'2026-04-11 03:09:22','2026-04-11 03:15:04'),(66,'App\\Models\\User',2,'api-token','4a7d53f25a6e6444bff21b8a56f10f33cb19d79c83b3490448fff65bf4465fd3','[\"*\"]','2026-04-11 05:08:19',NULL,'2026-04-11 04:03:46','2026-04-11 05:08:19'),(67,'App\\Models\\User',2,'api-token','6568ba355717c3679f9b9523560f40715e76ff6735760778c44ea31f9a66ed9f','[\"*\"]','2026-04-11 05:14:55',NULL,'2026-04-11 05:13:34','2026-04-11 05:14:55'),(68,'App\\Models\\User',2,'api-token','71da4237342dfc01727040fea71c59ca4a9b3c96fa69460d366a95aab286d69d','[\"*\"]','2026-04-11 12:07:06',NULL,'2026-04-11 11:42:09','2026-04-11 12:07:06'),(69,'App\\Models\\User',2,'api-token','8835b781270c67aae260b033afc52a029e6ec067e3c622f70e93fd20d5c3803b','[\"*\"]','2026-04-11 12:26:56',NULL,'2026-04-11 12:19:09','2026-04-11 12:26:56'),(70,'App\\Models\\User',2,'api-token','2508727494993859ec6e5c1a8e5346ec50496f2704c36ed9e29130e2bccc64fe','[\"*\"]','2026-04-12 02:27:10',NULL,'2026-04-12 02:10:26','2026-04-12 02:27:10'),(71,'App\\Models\\User',2,'api-token','7a551f92924eb86e5fbe939a179620457755448cd49e715271ec688eef47d398','[\"*\"]','2026-04-12 04:15:29',NULL,'2026-04-12 03:46:01','2026-04-12 04:15:29'),(72,'App\\Models\\User',2,'api-token','92826fb73aafa88ee0cfb051fc7a1a8164288eb40e6d5381a29de53fd3cafee1','[\"*\"]',NULL,NULL,'2026-04-12 04:51:39','2026-04-12 04:51:39'),(73,'App\\Models\\User',2,'api-token','8a9d49424d61ed5718b495a391faece69bb23e2525436f80eb8ce971fa527e41','[\"*\"]','2026-04-12 04:54:43',NULL,'2026-04-12 04:54:41','2026-04-12 04:54:43'),(74,'App\\Models\\User',2,'api-token','895262e69ce483b73f709cce0471b3277dd9559afd0d9ad4087ee837e0caa014','[\"*\"]','2026-04-12 04:56:51',NULL,'2026-04-12 04:56:50','2026-04-12 04:56:51'),(75,'App\\Models\\User',2,'api-token','8a7eec3bd02f44abb7968ccab6f6961f8f0ab7a460d982768dd3e18a399c8d2d','[\"*\"]','2026-04-12 05:08:15',NULL,'2026-04-12 05:08:13','2026-04-12 05:08:15'),(76,'App\\Models\\User',2,'api-token','14a0ff08b4c58fd60c1ce4b08ec079a6254c1806e2bc9cbb266576ce9f52a0b1','[\"*\"]','2026-04-12 11:35:09',NULL,'2026-04-12 11:33:30','2026-04-12 11:35:09'),(77,'App\\Models\\User',2,'api-token','42e5f7636db14849c9e2dde8e2ac84ee771938576e2cd3ec7b00b68488f0fa1e','[\"*\"]','2026-04-12 15:01:01',NULL,'2026-04-12 11:53:23','2026-04-12 15:01:01'),(78,'App\\Models\\User',2,'api-token','17f9c35d81005d0a42f0ef4797c9be134d933aeff954ecd116a3a74aa956ca55','[\"*\"]','2026-04-12 14:50:35',NULL,'2026-04-12 14:50:32','2026-04-12 14:50:35'),(79,'App\\Models\\User',2,'api-token','7343a3f1a6f85e73f7158b8339247ee85f4b4e173daed9c61b4c8d23aaf333b4','[\"*\"]','2026-04-12 15:00:39',NULL,'2026-04-12 14:56:14','2026-04-12 15:00:39'),(80,'App\\Models\\User',2,'api-token','1a5d8f00e80ebab89c556a27c736bc656e848415e2fc99a4e93ddda8d5da4517','[\"*\"]','2026-04-12 15:02:04',NULL,'2026-04-12 15:02:04','2026-04-12 15:02:04'),(81,'App\\Models\\User',2,'api-token','67d6138ec4771318a5d7182ddc81a6b548da0e04df3e748b51eb880cf73ae8c9','[\"*\"]','2026-04-12 15:23:45',NULL,'2026-04-12 15:02:31','2026-04-12 15:23:45'),(82,'App\\Models\\User',2,'api-token','dcf0cef83077ffc944fc0188c2f753911ecbccbd9a6ffcca64144320a27536f0','[\"*\"]','2026-04-12 15:36:02',NULL,'2026-04-12 15:29:32','2026-04-12 15:36:02'),(83,'App\\Models\\User',2,'api-token','3f27697d3e3af62a7fbcdef0d4f7b01fa6815ed4532bdbdef7023799a39d7aed','[\"*\"]','2026-04-12 15:57:25',NULL,'2026-04-12 15:45:13','2026-04-12 15:57:25'),(84,'App\\Models\\User',2,'api-token','42db7757cd25742b892e2b1932148ee8242d65c1f342018fcf71bcd0da385e41','[\"*\"]','2026-04-13 01:13:06',NULL,'2026-04-13 01:12:16','2026-04-13 01:13:06'),(85,'App\\Models\\User',2,'api-token','2c0ace63bf0506ccb6fe112819a16f8da0479a3096fae1933865a34a18c66889','[\"*\"]','2026-04-13 04:36:11',NULL,'2026-04-13 03:17:19','2026-04-13 04:36:11'),(86,'App\\Models\\User',2,'api-token','6c55a603ab0c5c139597df1007420b6b05c7bfbf0c1bd110020cb486bb1c7792','[\"*\"]','2026-04-13 13:22:49',NULL,'2026-04-13 10:41:58','2026-04-13 13:22:49'),(87,'App\\Models\\User',2,'api-token','c9c1705f4e3636780f725f7fb707e66626c73b5f80e3b2ec3c23079b852357c7','[\"*\"]','2026-04-13 13:54:02',NULL,'2026-04-13 13:50:56','2026-04-13 13:54:02'),(88,'App\\Models\\User',2,'api-token','28679e997625537050e5f78a2560164879085cb8e287770afa4da0fcbf2baf2b','[\"*\"]','2026-04-14 02:12:22',NULL,'2026-04-14 02:07:57','2026-04-14 02:12:22'),(89,'App\\Models\\User',2,'api-token','613da22a203b87777ad01df3d2ce800c94f5132d2cd2b8f5da4e77ff004a2f58','[\"*\"]','2026-04-14 02:23:53',NULL,'2026-04-14 02:08:15','2026-04-14 02:23:53'),(90,'App\\Models\\User',2,'api-token','692b206e03d95c3ed04ef63c65618b09b5067f573a183703a9415ab3d1330810','[\"*\"]','2026-04-14 02:39:47',NULL,'2026-04-14 02:37:46','2026-04-14 02:39:47'),(91,'App\\Models\\User',2,'api-token','1adcba5d29d0aec69ee9183e00e7d54a8fb2aae7b2bc4718603645482b3825ae','[\"*\"]','2026-04-14 03:25:28',NULL,'2026-04-14 03:03:10','2026-04-14 03:25:28'),(92,'App\\Models\\User',2,'api-token','fb0c1043e28bd2ce3e508c95fb9cf8b2e61aba36b0306da3a346b425a2187c71','[\"*\"]','2026-04-14 04:56:43',NULL,'2026-04-14 03:48:54','2026-04-14 04:56:43'),(93,'App\\Models\\User',2,'api-token','9d93b62c74e6598f90504d1bd46557ddfa6f3570e7e4024367ae8aafe259f38c','[\"*\"]','2026-04-14 11:23:53',NULL,'2026-04-14 10:35:58','2026-04-14 11:23:53'),(94,'App\\Models\\User',2,'api-token','7e4bc4cc37d353bfe9e0aef3cd1d61b8c72035bf68fecbe2cf06999ba4f0c4fd','[\"*\"]','2026-04-14 13:09:35',NULL,'2026-04-14 12:02:54','2026-04-14 13:09:35'),(95,'App\\Models\\User',2,'api-token','136c48efed272e5a61f26f075c0fd9b00070c5e5d9c3dbdda89a898b89814bfa','[\"*\"]','2026-04-14 14:10:48',NULL,'2026-04-14 13:13:09','2026-04-14 14:10:48'),(96,'App\\Models\\User',2,'api-token','8d234949c6b614e86443729178630a668aab5076167686007a8994f0560c33e4','[\"*\"]','2026-04-15 12:41:21',NULL,'2026-04-15 10:51:56','2026-04-15 12:41:21'),(97,'App\\Models\\User',2,'api-token','a3cd73b37b1ae72bd099759588d721c5e629c3d1b37e961afd512097e17cd47d','[\"*\"]','2026-04-15 14:10:07',NULL,'2026-04-15 13:37:48','2026-04-15 14:10:07'),(98,'App\\Models\\User',2,'api-token','f547fb8b949af2e1c09b2909716f2583b3b9fca9d2aa47f0c758a50f8a362d28','[\"*\"]','2026-04-16 02:20:48',NULL,'2026-04-16 01:37:45','2026-04-16 02:20:48'),(99,'App\\Models\\User',2,'api-token','d52899500d34d3a90fb6b3c87c317e98d3f11860147d6f1d3ee1e25082e3852e','[\"*\"]','2026-04-16 02:02:37',NULL,'2026-04-16 02:02:32','2026-04-16 02:02:37'),(100,'App\\Models\\User',2,'api-token','bf17a61185122a9703818a91f36d86cb43a91ed572f54be1197c903e6e39d177','[\"*\"]','2026-04-16 03:40:53',NULL,'2026-04-16 03:27:02','2026-04-16 03:40:53'),(101,'App\\Models\\User',2,'api-token','8c1fa43556fd859c82f91332ca722949ecb51aa8626d1a70831f3a3dadac28c8','[\"*\"]','2026-04-16 05:03:58',NULL,'2026-04-16 04:20:50','2026-04-16 05:03:58'),(102,'App\\Models\\User',2,'api-token','0ca3d3fb5223afea84599a542fd254535f53409461d717d17040bb10ff9c510d','[\"*\"]','2026-04-16 14:06:27',NULL,'2026-04-16 10:53:34','2026-04-16 14:06:27'),(103,'App\\Models\\User',2,'api-token','5b17c6ff8b2f994b148365312bc83c6286c2d7ddf4bc42df94eb93464f48f55b','[\"*\"]','2026-04-17 03:05:47',NULL,'2026-04-17 01:07:47','2026-04-17 03:05:47'),(104,'App\\Models\\User',2,'api-token','c87dbfc57584cdd6179cf475e8d330da7a67ff31366654b752174d66e7ec6d90','[\"*\"]','2026-04-17 04:44:55',NULL,'2026-04-17 02:56:24','2026-04-17 04:44:55'),(105,'App\\Models\\User',2,'api-token','3b67019b911843914a6ce626ec5f3c2eb3f1f1b4a9b93fddf979bf39251fac34','[\"*\"]','2026-04-17 05:02:46',NULL,'2026-04-17 04:52:51','2026-04-17 05:02:46'),(106,'App\\Models\\User',2,'api-token','168c16e872d8ec12a7fbca14a34ad6fb8318011d519450b3b0d7c60bbaa39c8f','[\"*\"]','2026-04-17 11:45:54',NULL,'2026-04-17 11:39:05','2026-04-17 11:45:54'),(107,'App\\Models\\User',2,'api-token','c64c87d9231b38eef80940b9c7e554c6351b5ff553570fcfc3ff7c75ae029536','[\"*\"]','2026-04-17 12:00:19',NULL,'2026-04-17 11:49:23','2026-04-17 12:00:19'),(108,'App\\Models\\User',2,'api-token','07be3563af543a3fb3bcf9f91601e2c8b9916765db0ce2dfafd519240509a453','[\"*\"]',NULL,NULL,'2026-04-17 13:53:36','2026-04-17 13:53:36'),(109,'App\\Models\\User',2,'api-token','f5696b5aaa93ac0fe04d6bc067a86e44e337a3c870205e4633d01d5822e9dc18','[\"*\"]',NULL,NULL,'2026-04-17 13:58:32','2026-04-17 13:58:32'),(110,'App\\Models\\User',2,'api-token','31205943949eadb9766520cf79c882f1ce345089e87013fbae3ca1e9691291f4','[\"*\"]','2026-04-17 14:10:32',NULL,'2026-04-17 14:03:16','2026-04-17 14:10:32'),(111,'App\\Models\\User',2,'api-token','68e31af867bfd2a7ead56287c8cb3626bf9aa9f48d37b244e692f6727710378e','[\"*\"]','2026-04-17 14:33:49',NULL,'2026-04-17 14:16:08','2026-04-17 14:33:49'),(112,'App\\Models\\User',5,'api-token','e4a8c2805330c5695fa9ae7cc9c69cf6555cd6a8177cbe13daeed393deb8351f','[\"*\"]','2026-04-18 02:46:55',NULL,'2026-04-18 02:46:28','2026-04-18 02:46:55'),(113,'App\\Models\\User',5,'api-token','2fe93d9316ed26fa009fea49528d47d9ced8d94f479d5693c8aeb4650b7ec77f','[\"*\"]','2026-04-18 02:53:45',NULL,'2026-04-18 02:53:39','2026-04-18 02:53:45'),(114,'App\\Models\\User',5,'api-token','7fad43febc15cf7e8f92b6804ac35e4bfacf57e5089af8e02d248c88ffcec2b5','[\"*\"]','2026-04-18 03:16:38',NULL,'2026-04-18 03:00:13','2026-04-18 03:16:38'),(115,'App\\Models\\User',2,'api-token','38ff7146959b04abe7a17b3169a9d12ec888840d6a1c4529b960c0b67c9a44bf','[\"*\"]','2026-04-18 03:31:31',NULL,'2026-04-18 03:31:24','2026-04-18 03:31:31'),(116,'App\\Models\\User',5,'api-token','9a4e1d8b78d68877c666706012a22225da6669c2121eb4049ad293b62cf8ecef','[\"*\"]','2026-04-18 05:38:19',NULL,'2026-04-18 05:25:38','2026-04-18 05:38:19'),(117,'App\\Models\\User',2,'api-token','c6c724a4f6e38d7ba0078247421060bf329acc701033ea51fca2c3d3aa78ed0b','[\"*\"]','2026-04-18 05:40:58',NULL,'2026-04-18 05:39:34','2026-04-18 05:40:58'),(118,'App\\Models\\User',5,'api-token','a064d46e30a07da35138f370c258c675445da0568ee7cb2e4de2c5983754d2e6','[\"*\"]','2026-04-18 11:10:01',NULL,'2026-04-18 10:53:03','2026-04-18 11:10:01'),(119,'App\\Models\\User',5,'api-token','68ee8f760442f921937c681ff927da779c6b4e2e4b9a8e9e5eb297327d7a0030','[\"*\"]','2026-04-18 13:35:00',NULL,'2026-04-18 13:33:53','2026-04-18 13:35:00'),(120,'App\\Models\\User',5,'api-token','b42f96f2db688d71819b1f55be5f04287ac64b34b43f7496b9413dd15b2ae064','[\"*\"]','2026-04-19 11:05:37',NULL,'2026-04-18 13:43:32','2026-04-19 11:05:37'),(121,'App\\Models\\User',5,'api-token','5e5f7a9b33cb95a05293156ca09971282bdde60513a062277f37a040f97932b2','[\"*\"]','2026-04-18 14:26:00',NULL,'2026-04-18 14:00:56','2026-04-18 14:26:00'),(122,'App\\Models\\User',5,'api-token','d72787ff8bf6f1cc12cfe9f1c949e91713601cbae96045a6e088ac3d57ebf312','[\"*\"]','2026-04-18 14:59:37',NULL,'2026-04-18 14:26:27','2026-04-18 14:59:37'),(123,'App\\Models\\User',5,'api-token','3140b5713877fc2b0328385620f24563d3dd494494fe1a9fc12e7cbc7a73ed29','[\"*\"]','2026-04-19 01:55:29',NULL,'2026-04-19 01:34:10','2026-04-19 01:55:29'),(124,'App\\Models\\User',2,'api-token','2f70b71aef1f53c7bd069b152e6bcfc79fc28a8a7caee2ea7d152c45ac6c61d6','[\"*\"]','2026-04-19 01:56:02',NULL,'2026-04-19 01:55:57','2026-04-19 01:56:02'),(125,'App\\Models\\User',2,'api-token','23d73d62bba2e4c227bdd29d73acbbd5fa485ea98b9dff0bde946bac4b58a2c2','[\"*\"]','2026-04-19 02:00:53',NULL,'2026-04-19 01:59:43','2026-04-19 02:00:53'),(126,'App\\Models\\User',2,'api-token','32146a157a8d740aa631d3ea846214914e513bc40621077cca62511c74f8e80d','[\"*\"]','2026-04-19 02:09:35',NULL,'2026-04-19 02:01:08','2026-04-19 02:09:35'),(127,'App\\Models\\User',5,'api-token','df627a4f1f6f3aa73cf68d3a26060024af532359dc60d27dbd96f95c825781e2','[\"*\"]','2026-04-19 02:14:04',NULL,'2026-04-19 02:13:33','2026-04-19 02:14:04'),(128,'App\\Models\\User',2,'api-token','ccd9d6c285d3ec08f615e4676fffe291464e67b875f27e53538a62cdad9d8ff4','[\"*\"]','2026-04-19 02:57:10',NULL,'2026-04-19 02:16:14','2026-04-19 02:57:10'),(129,'App\\Models\\User',5,'api-token','351ed9562dcb32369a0e021157a3f2c9add98077bcf4d15d8928ab3fdfe2505d','[\"*\"]','2026-04-19 04:39:32',NULL,'2026-04-19 03:39:03','2026-04-19 04:39:32'),(130,'App\\Models\\User',2,'api-token','9c0c2ab40edf5c6810e1886ff1c2af9e00579d60138b5ce82bde02b7f044edfa','[\"*\"]','2026-04-19 04:40:51',NULL,'2026-04-19 04:40:02','2026-04-19 04:40:51'),(131,'App\\Models\\User',5,'api-token','6ae8012adaced9c9d199c93575729c11a71ff5dc5e66e8589e159bc0f461ae4d','[\"*\"]','2026-04-19 05:21:30',NULL,'2026-04-19 04:50:33','2026-04-19 05:21:30'),(132,'App\\Models\\User',5,'api-token','376caf9b1c9a0c374ad7db71839d12621a607eec7775ca40eb43a6775d390421','[\"*\"]','2026-04-19 14:47:43',NULL,'2026-04-19 10:48:24','2026-04-19 14:47:43'),(133,'App\\Models\\User',2,'api-token','c17751b0d4984ec62ed473b9df1d6b3d0ac679648fd722dd5b2fea43c77bcf69','[\"*\"]','2026-04-19 12:16:19',NULL,'2026-04-19 12:14:58','2026-04-19 12:16:19'),(134,'App\\Models\\User',5,'api-token','59eb9f9ecb186e7ee573a5377deeb3961786fc03eafbdc8dbbd560ee8f131e59','[\"*\"]','2026-04-19 12:22:04',NULL,'2026-04-19 12:16:52','2026-04-19 12:22:04'),(135,'App\\Models\\User',2,'api-token','5421b88a7bdede9fcd2705e26df2b2c2311f63a363f7d1bb74cfd75725e4f037','[\"*\"]','2026-04-19 12:23:04',NULL,'2026-04-19 12:22:24','2026-04-19 12:23:04'),(136,'App\\Models\\User',5,'api-token','13663b319c78d6bb5ac06052e5af1c83d34e75b81c868388bb8271ba386fcdbb','[\"*\"]','2026-04-19 13:20:41',NULL,'2026-04-19 12:23:44','2026-04-19 13:20:41'),(137,'App\\Models\\User',5,'api-token','90ec35abaf29ce5b0565a4a9384d3eb2abdddffb931e2012d525b9ea701f6464','[\"*\"]','2026-04-19 14:52:34',NULL,'2026-04-19 13:27:01','2026-04-19 14:52:34'),(138,'App\\Models\\User',5,'api-token','a3b08664e28f51934855c28018afa00880ef10c13c3ccdd47eb96f40bdc89200','[\"*\"]','2026-04-20 12:07:08',NULL,'2026-04-20 10:10:31','2026-04-20 12:07:08'),(139,'App\\Models\\User',5,'api-token','b9dbaf3371c5e8af14cb470537d1ed1979dd1630ec7493cf396490a3e0cfa6c8','[\"*\"]','2026-04-20 13:21:11',NULL,'2026-04-20 12:34:46','2026-04-20 13:21:11'),(140,'App\\Models\\User',2,'api-token','f2a1d1a64ea5688ee5380e03c02fb336dc04478e897cfc4a3bd39049ee968f89','[\"*\"]','2026-04-20 13:23:31',NULL,'2026-04-20 13:21:44','2026-04-20 13:23:31'),(141,'App\\Models\\User',2,'api-token','be4e924b3647c08c697df5ffec079f0eb712d61f198cef1041a20a9deb1ffde6','[\"*\"]','2026-04-20 13:31:27',NULL,'2026-04-20 13:28:19','2026-04-20 13:31:27'),(142,'App\\Models\\User',2,'api-token','d407dc201fbd0a886d4276da1b983e829b76b9ba938943c5e39a80df43baf1b4','[\"*\"]','2026-04-20 15:00:00',NULL,'2026-04-20 13:36:51','2026-04-20 15:00:00'),(143,'App\\Models\\User',2,'api-token','6ac9f46e1bf7e088193c6ead83e5141f85265821fd257f8bd104c9c77e2da02f','[\"*\"]','2026-04-21 03:25:30',NULL,'2026-04-21 03:00:10','2026-04-21 03:25:30'),(144,'App\\Models\\User',2,'api-token','080fbf1d5b816fc85f996fd74db5304191d560ceba38ed507a8c15a7257bdd00','[\"*\"]','2026-04-21 04:40:44',NULL,'2026-04-21 03:54:24','2026-04-21 04:40:44'),(145,'App\\Models\\User',2,'api-token','799069cf2bfd313d6ce92d0b07582abc3b1c2b184f39d5eb57f9663d0bde82ee','[\"*\"]','2026-04-21 05:14:48',NULL,'2026-04-21 04:46:16','2026-04-21 05:14:48'),(146,'App\\Models\\User',2,'api-token','bb8faf312effe908c8b6a2ddf9a66401fcea4f15cd69e40969f8fa91a5f47d78','[\"*\"]','2026-04-21 13:15:59',NULL,'2026-04-21 11:04:38','2026-04-21 13:15:59'),(147,'App\\Models\\User',5,'api-token','7d79f0ac3a8d5ff0396c0e298f5fe1c5ad1569cac13ab9c41e42d0f1f73de01a','[\"*\"]','2026-04-21 14:34:01',NULL,'2026-04-21 13:16:19','2026-04-21 14:34:01'),(148,'App\\Models\\User',2,'api-token','007d871b8a0e47951607e742db141793ea90b7ceee4460a229c63a5ff320f993','[\"*\"]','2026-04-21 15:00:36',NULL,'2026-04-21 14:36:51','2026-04-21 15:00:36'),(149,'App\\Models\\User',2,'api-token','96c16bc5ab12cd7416f2816b4a1c1eceb115c8e62a0c0090d28e9d30d07262d2','[\"*\"]','2026-04-22 02:53:12',NULL,'2026-04-22 02:15:54','2026-04-22 02:53:12'),(150,'App\\Models\\User',2,'api-token','016a20c6ec45131e5d4e82febd8cb763c4aadd25a6e0bad6be5f681580198e34','[\"*\"]','2026-04-22 04:40:19',NULL,'2026-04-22 03:31:20','2026-04-22 04:40:19'),(151,'App\\Models\\User',6,'api-token','5eec9b111e3000b25f999b30f5c5d2de89990daca4169463c5e33550af2c3420','[\"*\"]',NULL,NULL,'2026-04-22 10:29:08','2026-04-22 10:29:08'),(152,'App\\Models\\User',6,'api-token','fe1e14f318bf7345e46286e7d8c1c46d7f17a098b8cb52f2b5cd3a01d6399a2b','[\"*\"]','2026-04-22 10:34:34',NULL,'2026-04-22 10:34:26','2026-04-22 10:34:34'),(153,'App\\Models\\User',7,'api-token','a1e2f3dd5e6db9991a75bd30d9f7de754cce3590e10a6fea12706fdeaf19662e','[\"*\"]',NULL,NULL,'2026-04-22 10:35:51','2026-04-22 10:35:51'),(154,'App\\Models\\User',8,'api-token','ef5bfa6799f56a0cbf3bd561f32f8e45512136335dd803ab3bfd52dc3a8a08c1','[\"*\"]',NULL,NULL,'2026-04-22 10:37:41','2026-04-22 10:37:41'),(155,'App\\Models\\User',9,'api-token','aa7e23dcd2078bdb2b05e7f1ed1d6d93eb48e7e9283a14da11457dbcc8c37dc3','[\"*\"]',NULL,NULL,'2026-04-22 10:49:16','2026-04-22 10:49:16'),(156,'App\\Models\\User',9,'api-token','909134ee092ad8e3db6d5db66992993c80c323693a9db95d4cdc9c06b58342b6','[\"*\"]','2026-04-22 13:16:20',NULL,'2026-04-22 10:49:18','2026-04-22 13:16:20'),(157,'App\\Models\\User',5,'api-token','030295cdc1d28184ba9f2b54d354e0023c59c6681e14d642e81e3a5a3f65da4f','[\"*\"]','2026-04-22 13:44:08',NULL,'2026-04-22 13:16:58','2026-04-22 13:44:08'),(158,'App\\Models\\User',2,'api-token','036eb169f4fd3906c2ecd8fe18e26be4d91ef3fe6040336cb9c2d7622c9978f9','[\"*\"]','2026-04-22 13:47:18',NULL,'2026-04-22 13:44:37','2026-04-22 13:47:18'),(159,'App\\Models\\User',5,'api-token','38d86148e47d951304ea63c62b48010715e7efe6cad03545d4ed394be0e313b9','[\"*\"]','2026-04-22 13:57:57',NULL,'2026-04-22 13:47:39','2026-04-22 13:57:57'),(160,'App\\Models\\User',2,'api-token','eda56ab6990a702597f94e2c7173a8af39f5828a81da9dbb40ea0cb328f4a93a','[\"*\"]','2026-04-22 14:07:18',NULL,'2026-04-22 13:58:15','2026-04-22 14:07:18'),(161,'App\\Models\\User',2,'api-token','e869249fc8c61b74788e3e39a01a1d2fcd0beea0d47de2f324aa7ca5917cbb57','[\"*\"]','2026-04-23 04:16:22',NULL,'2026-04-23 04:13:39','2026-04-23 04:16:22'),(162,'App\\Models\\User',2,'api-token','2dcd30a03fdbd0bcb3f6d0915c8cc4eb9fdeda9bc8483df518fd8f6a6622dbd0','[\"*\"]','2026-04-23 04:54:14',NULL,'2026-04-23 04:21:35','2026-04-23 04:54:14'),(163,'App\\Models\\User',2,'api-token','b41e17a4170dbda9db088f702891f473d9c9654d18a7ddba740d3988235cba22','[\"*\"]','2026-04-23 10:45:58',NULL,'2026-04-23 10:36:51','2026-04-23 10:45:58'),(164,'App\\Models\\User',2,'api-token','1c466c8c0f0b99957f9c4163890d8b2e2271f7b596d716498d9ccd94eac8950e','[\"*\"]','2026-04-23 12:43:25',NULL,'2026-04-23 12:22:24','2026-04-23 12:43:25'),(165,'App\\Models\\User',5,'api-token','bb8f8dbdaf3b1d483a6ad1a65b7fb58ae4986b89f3f42be0afb8a86f275488aa','[\"*\"]','2026-04-23 14:21:22',NULL,'2026-04-23 13:43:03','2026-04-23 14:21:22'),(166,'App\\Models\\User',2,'api-token','8d98e5630df712022844c61fe1a898b44dc2cd40055dd1db628ec41471b3327d','[\"*\"]','2026-04-23 14:23:38',NULL,'2026-04-23 14:21:55','2026-04-23 14:23:38'),(167,'App\\Models\\User',5,'api-token','8cc370203f0f7e62334b300f61d96f01c8fb5244ab2d91d62da57adf0ce457e8','[\"*\"]','2026-04-23 14:30:00',NULL,'2026-04-23 14:24:34','2026-04-23 14:30:00'),(168,'App\\Models\\User',2,'api-token','02a98239d5229d69e2eeb2794e392b318dacfd20bce6b361d447ff65177f67f5','[\"*\"]','2026-04-25 03:10:23',NULL,'2026-04-25 03:10:23','2026-04-25 03:10:23'),(169,'App\\Models\\User',2,'api-token','acc6c641de000034dbe72949bcb9f96059057ca44f31efca1eb42b18237803a7','[\"*\"]','2026-04-25 03:12:37',NULL,'2026-04-25 03:11:15','2026-04-25 03:12:37');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pertumbuhan_anaks`
--

DROP TABLE IF EXISTS `pertumbuhan_anaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pertumbuhan_anaks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anak_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `berat_badan` double DEFAULT NULL,
  `tinggi_badan` double DEFAULT NULL,
  `lingkar_kepala` double DEFAULT NULL,
  `z_score_bb` double DEFAULT NULL,
  `z_score_tb` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pertumbuhan_anaks_anak_id_foreign` (`anak_id`),
  CONSTRAINT `pertumbuhan_anaks_anak_id_foreign` FOREIGN KEY (`anak_id`) REFERENCES `anaks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pertumbuhan_anaks`
--

LOCK TABLES `pertumbuhan_anaks` WRITE;
/*!40000 ALTER TABLE `pertumbuhan_anaks` DISABLE KEYS */;
INSERT INTO `pertumbuhan_anaks` VALUES (1,1,'2026-03-12',7,72,55,-1.13,2,'2026-04-12 13:29:02','2026-04-13 04:10:17'),(2,1,'2026-04-13',7.5,73,55,-0.5,2.4,'2026-04-13 04:11:03','2026-04-13 04:35:51'),(3,18,'2026-04-19',7.5,68.2,42.1,-2.1,-2.27,'2026-04-19 11:36:06','2026-04-19 11:36:06');
/*!40000 ALTER TABLE `pertumbuhan_anaks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pmba_details`
--

DROP TABLE IF EXISTS `pmba_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pmba_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pmba_id` bigint(20) unsigned NOT NULL,
  `karbohidrat` tinyint(1) NOT NULL DEFAULT 0,
  `protein_hewani` tinyint(1) NOT NULL DEFAULT 0,
  `protein_nabati` tinyint(1) NOT NULL DEFAULT 0,
  `sayur` tinyint(1) NOT NULL DEFAULT 0,
  `buah` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kacang` tinyint(1) NOT NULL DEFAULT 0,
  `susu` tinyint(1) NOT NULL DEFAULT 0,
  `telur` tinyint(1) NOT NULL DEFAULT 0,
  `vitamin_a` tinyint(1) NOT NULL DEFAULT 0,
  `asi` tinyint(1) NOT NULL DEFAULT 0,
  `skor` int(11) DEFAULT NULL,
  `status` enum('kurang','cukup','baik') DEFAULT NULL,
  `mdd_score` int(11) DEFAULT NULL,
  `mmf_status` tinyint(1) DEFAULT NULL,
  `mad_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pmba_details_pmba_id_foreign` (`pmba_id`),
  CONSTRAINT `pmba_details_pmba_id_foreign` FOREIGN KEY (`pmba_id`) REFERENCES `pmbas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pmba_details`
--

LOCK TABLES `pmba_details` WRITE;
/*!40000 ALTER TABLE `pmba_details` DISABLE KEYS */;
INSERT INTO `pmba_details` VALUES (1,1,1,1,0,0,1,'2026-04-09 13:53:12','2026-04-09 13:53:12',1,1,0,0,1,5,'cukup',5,1,1),(3,4,1,1,1,1,1,'2026-04-10 14:21:05','2026-04-11 05:07:47',1,0,0,1,1,7,'baik',6,1,1),(4,5,1,0,1,1,0,'2026-04-11 04:09:35','2026-04-11 04:09:35',0,0,1,1,1,5,'cukup',5,1,1),(5,7,1,0,0,0,1,'2026-04-12 15:15:17','2026-04-12 15:36:02',0,1,0,0,1,3,'kurang',4,1,0),(6,10,1,1,1,1,1,'2026-04-14 02:12:13','2026-04-14 02:12:13',1,0,1,0,0,7,'baik',5,1,1),(7,11,1,1,1,1,1,'2026-04-14 02:39:45','2026-04-14 02:39:45',0,0,0,0,1,5,'cukup',5,1,1),(8,12,1,1,0,1,0,'2026-04-16 01:42:47','2026-04-16 01:42:47',0,0,1,0,1,4,'cukup',4,1,0),(9,13,1,1,0,1,1,'2026-04-17 01:31:15','2026-04-17 01:31:15',1,0,1,0,1,6,'cukup',5,0,0),(10,14,1,1,1,1,1,'2026-04-17 01:41:53','2026-04-17 01:41:53',0,1,1,0,0,7,'baik',6,1,1),(11,15,1,1,1,1,1,'2026-04-17 01:44:33','2026-04-17 01:44:33',1,1,1,0,0,8,'baik',6,1,1),(12,16,1,1,1,1,1,'2026-04-17 01:46:47','2026-04-17 01:46:47',1,1,1,0,0,8,'baik',6,1,1),(13,17,1,1,0,1,1,'2026-04-17 01:51:49','2026-04-17 01:51:49',0,1,1,0,0,6,'cukup',5,1,1),(14,18,1,1,1,1,0,'2026-04-17 02:07:03','2026-04-17 02:07:03',1,0,1,0,1,6,'cukup',5,1,1),(15,19,1,1,1,1,1,'2026-04-17 03:05:45','2026-04-17 03:05:45',0,0,1,0,1,6,'cukup',6,1,1),(16,20,1,1,1,1,0,'2026-04-19 12:12:07','2026-04-19 12:23:09',1,0,1,0,1,6,'cukup',5,1,1),(17,21,1,1,1,1,1,'2026-04-25 03:12:33','2026-04-25 03:12:33',1,0,0,0,1,6,'cukup',5,1,1);
/*!40000 ALTER TABLE `pmba_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pmbas`
--

DROP TABLE IF EXISTS `pmbas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pmbas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anak_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `usia_bulan` int(11) NOT NULL,
  `frekuensi_makan` int(11) DEFAULT NULL,
  `tekstur` enum('lumat','lembek','padat') DEFAULT NULL,
  `porsi` enum('kurang','cukup') DEFAULT NULL,
  `sumber_makanan` enum('rumahan','instan','campuran') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipe` enum('pmba','mpasi') NOT NULL DEFAULT 'pmba',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pmbas_anak_id_tanggal_tipe_unique` (`anak_id`,`tanggal`,`tipe`),
  CONSTRAINT `pmbas_anak_id_foreign` FOREIGN KEY (`anak_id`) REFERENCES `anaks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pmbas`
--

LOCK TABLES `pmbas` WRITE;
/*!40000 ALTER TABLE `pmbas` DISABLE KEYS */;
INSERT INTO `pmbas` VALUES (1,1,'2026-04-09',7,3,'lumat','cukup',NULL,'2026-04-09 13:53:12','2026-04-09 13:53:12','pmba'),(4,1,'2026-04-10',7,3,'lumat','cukup',NULL,'2026-04-10 14:21:05','2026-04-10 14:21:05','pmba'),(5,1,'2026-04-11',7,4,'padat','cukup',NULL,'2026-04-11 04:09:35','2026-04-11 04:09:35','pmba'),(7,2,'2026-04-12',9,3,'padat','cukup',NULL,'2026-04-12 15:15:17','2026-04-12 15:15:17','pmba'),(10,3,'2026-04-14',28,3,'padat','cukup',NULL,'2026-04-14 02:12:13','2026-04-14 02:12:13','pmba'),(11,4,'2026-04-14',18,3,'padat','cukup',NULL,'2026-04-14 02:39:45','2026-04-14 02:39:45','pmba'),(12,5,'2026-04-16',9,3,'lembek','cukup',NULL,'2026-04-16 01:42:47','2026-04-16 01:42:47','pmba'),(13,6,'2026-04-17',14,2,'padat','cukup',NULL,'2026-04-17 01:31:15','2026-04-17 01:31:15','pmba'),(14,7,'2026-04-17',19,3,'padat','cukup',NULL,'2026-04-17 01:41:53','2026-04-17 01:41:53','pmba'),(15,8,'2026-04-17',15,3,'padat','cukup',NULL,'2026-04-17 01:44:33','2026-04-17 01:44:33','pmba'),(16,9,'2026-04-17',11,3,'padat','cukup',NULL,'2026-04-17 01:46:47','2026-04-17 01:46:47','pmba'),(17,10,'2026-04-17',13,3,'lembek','cukup',NULL,'2026-04-17 01:51:49','2026-04-17 01:51:49','pmba'),(18,11,'2026-04-17',13,3,'padat','cukup',NULL,'2026-04-17 02:07:03','2026-04-17 02:07:03','pmba'),(19,12,'2026-04-17',11,3,'padat','cukup',NULL,'2026-04-17 03:05:45','2026-04-17 03:05:45','pmba'),(20,18,'2026-04-19',4,4,'lembek','cukup',NULL,'2026-04-19 12:12:07','2026-04-19 12:23:09','pmba'),(21,12,'2026-04-25',12,3,'padat','cukup',NULL,'2026-04-25 03:12:33','2026-04-25 03:12:33','pmba');
/*!40000 ALTER TABLE `pmbas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remaja_putris`
--

DROP TABLE IF EXISTS `remaja_putris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remaja_putris` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `no_hp` varchar(255) DEFAULT NULL,
  `sekolah` varchar(255) DEFAULT NULL,
  `kelas` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `hb` int(11) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `sudah_menstruasi` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal_menstruasi_terakhir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remaja_putris`
--

LOCK TABLES `remaja_putris` WRITE;
/*!40000 ALTER TABLE `remaja_putris` DISABLE KEYS */;
INSERT INTO `remaja_putris` VALUES (1,'Remaja Try','2008-03-12','0814433212221','SMA 1','12','Kediri',10,60.00,155.00,1,'2026-03-15','2026-04-14 10:42:38','2026-04-14 10:42:38');
/*!40000 ALTER TABLE `remaja_putris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('dFN4ROWLfAXF3M8OJgU4ZrlMcsfX8RvKwrohnwiS',NULL,'15.222.24.115','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJYb0c3M0VyZHVuYWxvNXdkNnhSMlE2bEd1TjY2ZEpBeW0yQ2RoUTkxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9idW1pbC1tb25pdG9yaW5nLWFwaS5hcmlqYXlhc29mdHdhcmVob3VzZS5vbmxpbmUiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1777070997),('dkoiNyjkw5d4ETtnSchNUC4l3NOYuOqLZpybjvw7',NULL,'3.108.6.45','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJQSThyWXNScmF3YkdlOU43UFA3WjRCRG1tSjdhQnV4aTFCc1hVQzVlIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYnVtaWwtbW9uaXRvcmluZy1hcGkuYXJpamF5YXNvZnR3YXJlaG91c2Uub25saW5lIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1777072339),('gyRV9i9VnRpU8LUpi56Vkw7sRcpsDAlH8YvZGsdh',NULL,'2a02:4780:6:c0de::8','Go-http-client/2.0','eyJfdG9rZW4iOiI0SFU5S1JHMFY4d3FFeEwzVDEyQ1RZeXpJZ0FIdnJqWGVWTFFIWWRIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9idW1pbC1tb25pdG9yaW5nLWFwaS5hcmlqYXlhc29mdHdhcmVob3VzZS5vbmxpbmUiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1777000991),('k8yMeXp5dIkGKP2oxukjQMrZwE6FyGyZAbUDhpIe',NULL,'64.23.236.123','Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0','eyJfdG9rZW4iOiJDVUljb0RDQmluYzl0WkIwUE55RkFCZzVoUXZEbGowbUZCTmtneGVUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYnVtaWwtbW9uaXRvcmluZy1hcGkuYXJpamF5YXNvZnR3YXJlaG91c2Uub25saW5lIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1776933408),('LiTXtyZH4lNtKD8zLPsnAu4invAzfJMvgtzKmq5m',NULL,'2602:80d:1005::1e','Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)','eyJfdG9rZW4iOiI2MkZVdWZpazhQZWNJejZkbzhvMzZmN1Y5VlUzRWdndHNYcXRhemtBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cuYnVtaWwtbW9uaXRvcmluZy1hcGkuYXJpamF5YXNvZnR3YXJlaG91c2Uub25saW5lIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1777022767),('Ttws28n8aRo0nUruSAWdVLDgaiz4Zf5TFVPLSbcY',NULL,'2a14:7c1:400:21::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJNYUhidnZkUG9tVUh6V0s0YlVMcjZ3OUw5bGdqTUNGZnhCZFV3cnlJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9idW1pbC1tb25pdG9yaW5nLWFwaS5hcmlqYXlhc29mdHdhcmVob3VzZS5vbmxpbmUiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1777085258),('WI1Hkcox2WV4OPcaylr7izAGi8S68RU4HZ1Tu59C',NULL,'2a02:4780:6:c0de::8','Go-http-client/2.0','eyJfdG9rZW4iOiJUaHc3M01La0tuVnY0N0FLV1d1bks3NGVBVmN0UnNnbUFLNzVWaE94IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9idW1pbC1tb25pdG9yaW5nLWFwaS5hcmlqYXlhc29mdHdhcmVob3VzZS5vbmxpbmUiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1776934006);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ttd_ibus`
--

DROP TABLE IF EXISTS `ttd_ibus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ttd_ibus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ibu_id` bigint(20) unsigned NOT NULL,
  `kehamilan_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal_dapat` date DEFAULT NULL,
  `bulan_ke` int(11) DEFAULT NULL,
  `jumlah_diminum` int(11) NOT NULL DEFAULT 0,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ttd_ibus_ibu_id_foreign` (`ibu_id`),
  KEY `ttd_ibus_kehamilan_id_foreign` (`kehamilan_id`),
  CONSTRAINT `ttd_ibus_ibu_id_foreign` FOREIGN KEY (`ibu_id`) REFERENCES `ibus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ttd_ibus_kehamilan_id_foreign` FOREIGN KEY (`kehamilan_id`) REFERENCES `kehamilans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ttd_ibus`
--

LOCK TABLES `ttd_ibus` WRITE;
/*!40000 ALTER TABLE `ttd_ibus` DISABLE KEYS */;
/*!40000 ALTER TABLE `ttd_ibus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ttd_remajas`
--

DROP TABLE IF EXISTS `ttd_remajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ttd_remajas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `remaja_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah_minum` int(11) NOT NULL DEFAULT 0,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ttd_remajas_remaja_id_tanggal_unique` (`remaja_id`,`tanggal`),
  CONSTRAINT `ttd_remajas_remaja_id_foreign` FOREIGN KEY (`remaja_id`) REFERENCES `remaja_putris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ttd_remajas`
--

LOCK TABLES `ttd_remajas` WRITE;
/*!40000 ALTER TABLE `ttd_remajas` DISABLE KEYS */;
INSERT INTO `ttd_remajas` VALUES (1,1,'2026-04-14',3,NULL,'2026-04-14 13:44:43','2026-04-14 13:44:43');
/*!40000 ALTER TABLE `ttd_remajas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','bidan','kader','ibu','remaja') DEFAULT 'kader',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@gmail.com',NULL,'$2y$12$4hpAUfE42CfugioYCgmcTuSYoyTvdoJBU1MXxzjqXgaJ2xKxihebi$2y$12$UKiOBp6XKR/62Jz/4s4Y3O5jLF.R3ARn1Pi1Lh/XgYBGxZMmwoxxu','admin',NULL,'2026-03-30 14:32:09','2026-03-30 14:32:09'),(2,'Kader','kader@gmail.com',NULL,'$2y$12$Gy7NiCQ2hmQJjQdMIy9Hk.loutQPYUme6uurMXJXGzgtwIVUOs/Ua','kader',NULL,'2026-03-30 14:32:10','2026-03-30 14:32:10'),(3,'Bidan','bidan@gmail.com',NULL,'$2y$12$PygOtctTPLbDqW1k5zL12uzbdWGc9ALgC4QE1Mo1zer7exzVrKvna','bidan',NULL,'2026-03-30 14:32:10','2026-03-30 14:32:10'),(4,'Remaja','remaja@gmail.com',NULL,'$2y$12$5ISKrLTWvQJfBU8IWCK4wu1tQVz./UILAeMfTdHtVfD6MR6i52RRK','remaja',NULL,'2026-04-18 02:46:01','2026-04-18 02:46:01'),(5,'Ibu','ibu@gmail.com',NULL,'$2y$12$D0eLT3tOv14x9nn66.NXmenNAQBEggjxIIsa8WQ1iLl79k60K.0nu','ibu',NULL,'2026-04-18 02:46:02','2026-04-18 02:46:02'),(6,'Kader 2','kader@example.com',NULL,'$2y$12$HXnwR4A3eoSW5shbi9/xVONeNl/dHbMMsyOKXK887frdACdoKtO42','kader',NULL,'2026-04-22 10:29:08','2026-04-22 10:29:08'),(7,'Ibu Example','ibu@example.com',NULL,'$2y$12$VjRSWZUWPDAyOdpBjj/5Put3dccLgILc1R4KUWygdbnQy9pSKGRc.','ibu',NULL,'2026-04-22 10:35:51','2026-04-22 10:35:51'),(8,'Kader 3','kader3@example.com',NULL,'$2y$12$fqOPD0OZ6apWu2jCd5kdqeBx8Y8.5geZq3UwgDniv73vs6byzRrZi','kader',NULL,'2026-04-22 10:37:41','2026-04-22 10:37:41'),(9,'Kader 4','kader4@example.com',NULL,'$2y$12$GsrtM9wbewKwiusH6zVzMuwau7GRdvh/BX1QR5BlXO8QHHqf.bF0S','kader',NULL,'2026-04-22 10:49:16','2026-04-22 10:49:16');
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

-- Dump completed on 2026-04-25 11:02:16
