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

-- Dumping structure for table dbpbcms.bond_information_tmps
CREATE TABLE IF NOT EXISTS `bond_information_tmps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `binfo_category_id` int DEFAULT NULL,
  `binfo_subcategory_id` int DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text,
  `file_name` varchar(255) DEFAULT NULL,
  `file` blob,
  `file_mime` varchar(250) DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `form_status` int NOT NULL,
  `action` varchar(250) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `modified_by` varchar(50) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `master_id` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table dbpbcms.bond_information_tmps: ~0 rows (approximately)
INSERT INTO `bond_information_tmps` (`id`, `binfo_category_id`, `binfo_subcategory_id`, `title`, `description`, `file_name`, `file`, `file_mime`, `status`, `form_status`, `action`, `created_by`, `modified_by`, `comments`, `master_id`, `updated_by`, `created_at`, `updated_at`, `ip`) VALUES
	(51, 11, 12, 'Savings account opening form', '- 12% Element <br>\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element', 'savings_account_opening_form_attach_bb_16870810765.pdf', NULL, NULL, 0, 7, 'Inactive', 1, NULL, 'rrr', 8, 1, '2024-09-26 11:08:17', '2024-09-26 11:09:55', '::1'),
	(52, 7, 8, '545646', 'mknn', '545646_attach_bb_16393116011.pdf', NULL, NULL, 0, 7, 'Inactive', 1, NULL, '333', 4, 1, '2024-09-26 11:10:26', '2024-09-26 11:10:40', '::1'),
	(53, 11, 12, 'Savings account opening form', '- 12% Element <br>\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element', 'savings_account_opening_form_attach_bb_16870810765.pdf', NULL, NULL, 0, 7, 'Inactive', 1, NULL, 'rr', 8, 1, '2024-09-26 11:24:51', '2024-09-26 11:25:48', '::1'),
	(54, 5, 6, 'ok', 'ok', 'ok_attach_bb_16418788659.docx', NULL, NULL, 0, 7, 'Inactive', 1, NULL, '444', 6, 1, '2024-09-26 11:25:08', '2024-09-26 11:26:05', '::1'),
	(55, 11, 12, 'Savings account opening form', '- 12% Element <br>\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element\r\n- 12% Element\r\n- 25% Element\r\n- 22% Element', 'savings_account_opening_form_attach_bb_16870810765.pdf', NULL, NULL, 0, 7, 'Inactive', 1, NULL, '5ty', 8, 1, '2024-09-26 11:25:33', '2024-09-26 11:26:17', '::1');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
