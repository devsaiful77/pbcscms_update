-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 17, 2024 at 11:50 AM
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
-- Table structure for table `group_info_tmps`
--

DROP TABLE IF EXISTS `group_info_tmps`;
CREATE TABLE IF NOT EXISTS `group_info_tmps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `department_id` bigint NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `group_level_id` int UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `action` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `form_status` int NOT NULL,
  `created_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `master_id` int DEFAULT NULL,
  `modified_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_info_tmps`
--

INSERT INTO `group_info_tmps` (`id`, `name`, `department_id`, `description`, `group_level_id`, `is_active`, `action`, `status`, `form_status`, `created_by`, `master_id`, `modified_by`, `ip`, `comments`, `created_at`, `updated_at`) VALUES
(62, 'Underwriting Whole Sale Banking Unit', 39, 'Underwriting Whole Sale Banking Unit dd', 0, 1, 'Edit', 2, -1, '1', 82, 'cex2', '::1', 'rejected', '2024-10-16 17:15:28', '2024-10-16 17:35:30'),
(58, 'Trade Development & Cash Management(Corporate Banking Division)', 54, NULL, 1, 1, 'Active', 1, 7, '6835', 121, NULL, '::1', 'ff', '2024-10-03 05:03:08', '2024-10-03 06:15:13'),
(59, 'Trade Development & Cash Management(Corporate Banking Division)', 54, NULL, 1, 1, 'Active', 1, 1, '6835', 121, 'cex', '::1', NULL, '2024-10-03 05:30:41', '2024-10-03 06:01:18'),
(63, 'Underwriting Whole Sale Banking Unit', 39, 'Underwriting Whole Sale Banking Unit', 0, 1, 'Edit', 2, 7, '1', 82, NULL, '::1', 'hh', '2024-10-16 18:06:45', '2024-10-16 18:45:58'),
(64, 'Underwriting Whole Sale Banking Unit', 39, 'Underwriting Whole Sale Banking Unit', 0, 1, 'Inactive', 0, 0, '1', 82, NULL, '::1', NULL, '2024-10-17 10:22:50', '2024-10-17 10:22:50'),
(65, 'Underwriting Whole Sale Banking Unit', 39, 'Underwriting Whole Sale Banking Unit', 0, 1, 'Inactive', 0, 0, '1', 82, NULL, '::1', NULL, '2024-10-17 10:23:00', '2024-10-17 10:23:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
