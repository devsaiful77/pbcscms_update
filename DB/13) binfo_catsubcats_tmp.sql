-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table dbpbcms.binfo_catsubcats_tmp
CREATE TABLE IF NOT EXISTS `binfo_catsubcats_tmp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT '0',
  `name` varchar(150) DEFAULT NULL,
  `description` text,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ip` varchar(16) NOT NULL,
  `action` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_status` int NOT NULL,
  `master_id` int DEFAULT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table dbpbcms.binfo_catsubcats_tmp: ~2 rows (approximately)
INSERT INTO `binfo_catsubcats_tmp` (`id`, `parent_id`, `name`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `ip`, `action`, `form_status`, `master_id`, `modified_by`, `comments`) VALUES
	(73, 0, 'jhdfhjsdb', 'sdsdsvsdv', 1, 1, NULL, '2024-09-24 15:39:22', '2024-09-24 15:40:15', '::1', 'Add', 7, NULL, NULL, NULL),
	(74, 0, 'add', 'sw\',;sf,', 1, 6848, 6848, '2024-09-26 10:52:46', '2024-09-26 10:52:46', '::1', 'Active', 0, 33, '6848', NULL),
	(75, 0, 'eee', 'eee', 1, 1, NULL, '2024-09-26 11:07:33', '2024-09-26 11:07:57', '::1', 'Add', 7, NULL, NULL, 'rrr');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
