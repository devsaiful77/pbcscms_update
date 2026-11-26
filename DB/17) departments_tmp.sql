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

-- Dumping structure for table dbpbcms.departments_tmp
CREATE TABLE IF NOT EXISTS `departments_tmp` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `division_id` bigint DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `form_status` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `master_id` int DEFAULT NULL,
  `modified_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table dbpbcms.departments_tmp: ~2 rows (approximately)
INSERT INTO `departments_tmp` (`id`, `name`, `action`, `description`, `division_id`, `status`, `form_status`, `created_by`, `master_id`, `modified_by`, `comments`, `ip`, `created_at`, `updated_at`) VALUES
	(60, 'XX DEPT', 'Inactive', 'X DEPT', 15, 0, 7, 1, 91, NULL, '555', '::1', '2024-09-24 13:41:59', '2024-09-26 04:46:05'),
	(61, 'www', 'Inactive', NULL, 12, 0, 7, 1, 103, NULL, 'rrr', '::1', '2024-09-24 13:42:18', '2024-09-26 05:06:04'),
	(62, 'tyu', 'Inactive', '', 14, 0, -1, 1, 98, 'as12', NULL, '::1', '2024-09-24 13:42:37', '2024-09-24 13:43:08'),
	(63, 'test dept 1', 'Inactive', 'test dept 1', 8, 0, 0, 1, 106, NULL, NULL, '::1', '2024-09-26 05:05:44', '2024-09-26 05:05:44');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
