-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 08, 2024 at 05:02 AM
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
-- Table structure for table `unit_items_tmp`
--

DROP TABLE IF EXISTS `unit_items_tmp`;
CREATE TABLE IF NOT EXISTS `unit_items_tmp` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `master_id` int DEFAULT NULL,
  `product_type_id` int DEFAULT NULL,
  `issues_from` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `auto_unit_id` int DEFAULT NULL,
  `name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sent_sms` tinyint(1) NOT NULL DEFAULT '0',
  `issue_categories_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `form_status` int NOT NULL,
  `action` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `modified_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_api` tinyint(1) NOT NULL DEFAULT '0',
  `is_ci` tinyint DEFAULT NULL,
  `is_ci_cif` tinyint NOT NULL DEFAULT '0',
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_type_id` (`product_type_id`),
  KEY `issue_categories_id` (`issue_categories_id`),
  KEY `master_id` (`master_id`),
  KEY `issues_from` (`issues_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
