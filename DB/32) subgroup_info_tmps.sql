-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 17, 2024 at 11:49 AM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbpbcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `subgroup_info_tmps`
--

DROP TABLE IF EXISTS `subgroup_info_tmps`;
CREATE TABLE IF NOT EXISTS `subgroup_info_tmps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `group_info_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  `form_status` int NOT NULL,
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `master_id` int DEFAULT NULL,
  `modified_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subgroup_info_department_id_foreign` (`department_id`),
  KEY `subgroup_info_group_info_id_foreign` (`group_info_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subgroup_info_tmps`
--

INSERT INTO `subgroup_info_tmps` (`id`, `department_id`, `group_info_id`, `name`, `description`, `is_active`, `status`, `form_status`, `created_by`, `master_id`, `modified_by`, `action`, `ip`, `comments`, `created_at`, `updated_at`) VALUES
(20, 4, 1, 'Zinzira Branch', 'Zinzira Branchs', 0, 2, 7, '6835', 77, NULL, 'Edit', '::1', 'ss', '2024-10-03 06:32:40', '2024-10-03 06:33:09'),
(21, 4, 1, 'Zinzira Branch', 'Zinzira Branch dd', 0, 2, -1, '1', 77, 'cex2', 'Edit', '::1', 'rejected', '2024-10-16 18:31:00', '2024-10-16 18:35:34'),
(22, 4, 1, 'Zinzira Branch', 'Zinzira Branch', 0, 2, 7, '1', 77, NULL, 'Edit', '::1', 'sendbacked', '2024-10-16 18:39:27', '2024-10-16 18:42:00'),
(23, 4, 1, 'Zinzira Branch', 'Zinzira Branch', 0, 0, 0, '1', 77, NULL, 'Inactive', '::1', NULL, '2024-10-17 10:32:51', '2024-10-17 10:32:51'),
(24, 4, 1, 'Zinzira Branch', 'Zinzira Branch', 0, 0, 0, '1', 77, NULL, 'Inactive', '::1', NULL, '2024-10-17 10:33:19', '2024-10-17 10:33:19'),
(25, 4, 1, 'Zinzira Branch', 'Zinzira Branch', 0, 0, 0, '1', 77, NULL, 'Inactive', '::1', NULL, '2024-10-17 10:33:45', '2024-10-17 10:33:45');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
