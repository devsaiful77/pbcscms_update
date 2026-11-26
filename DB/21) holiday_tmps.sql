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
-- Table structure for table `holiday_tmps`
--

DROP TABLE IF EXISTS `holiday_tmps`;
CREATE TABLE IF NOT EXISTS `holiday_tmps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `day` int DEFAULT NULL,
  `months` int DEFAULT NULL,
  `year` int DEFAULT NULL,
  `dates` date DEFAULT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `form_status` int NOT NULL,
  `importable_file` blob,
  `file_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_mime` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `master_id` int DEFAULT NULL,
  `modified_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holiday_tmps`
--

INSERT INTO `holiday_tmps` (`id`, `day`, `months`, `year`, `dates`, `type`, `remarks`, `status`, `form_status`, `importable_file`, `file_name`, `file_mime`, `created_by`, `master_id`, `modified_by`, `action`, `ip`, `comments`, `created_at`, `updated_at`) VALUES
(6, NULL, NULL, NULL, '2020-03-17', 'public', 'Sheikh Mujibur Rahman\'s Birthday', 0, 7, NULL, NULL, NULL, '6835', 2, NULL, 'Delete', '::1', 'a', '2024-10-03 07:14:24', '2024-10-03 07:15:27'),
(14, 9, 4, 2020, '2020-04-09', 'public', 'Shab e-Barat', 2, 7, NULL, NULL, NULL, '1', 4, NULL, 'Edit', '::1', 'ssddserfvfgdfgdfg', '2024-10-17 10:43:19', '2024-10-17 11:04:58');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
