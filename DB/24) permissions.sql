-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 03, 2024 at 10:05 AM
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `controller_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guard_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `controller_name`, `name`, `display_name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'DepartmentsController', 'accessDepartment', 'Access Department', 'Access all type of Department Activity', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(2, 'GroupInfoController', 'accessGroup', 'Access Group', 'Access All', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(3, 'SubgroupInfoController', 'accessSubgroup', 'Access Subgroup', 'Access All', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(4, 'UnitsController', 'accessUnits', 'Access Unit', NULL, 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(5, 'UnitItemsController', 'accessIssues', 'Access Issues', 'Access All', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(6, 'SupportsController', 'wformComplain', 'wformComplain', NULL, 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(7, 'SupportsController', 'supportExecutive', 'supportExecutive', NULL, 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(8, 'ReportsController', 'accessReport', 'Access Report', NULL, 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(9, 'SupportsController', 'Handler/Queue', 'Handler/Queue', NULL, 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(10, 'SupportsController', 'revokeAssignedRequest', 'Unassigned Request', NULL, 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(11, 'DashboardsController', 'accessDashboard', 'Access Dashboard', NULL, 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(12, 'UsersController', 'accessUser', 'User Access', 'User Management', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(13, 'DivisionsController', 'accessDivision', 'Access Division', 'Access all type of Division Activity', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(14, 'UnitItemsController', 'accessIssueCategories', 'Access Issue Categories', 'Access all type of Issue Categories', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(15, 'WorkflowController', 'accessWorkflow', 'Access Workflow', 'Access all type of Workflow', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(16, 'BondUploadsController', 'accessBondUpload', 'Access Bond Upload', 'Access all type of bond upload', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(17, 'BondUploadsController', 'accessBondInformation', 'Access Bond Information', 'Access all type of bond information', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(21, 'SupportsController', 'ceAnalysis', 'CE Analysis', 'Complaing Closing', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(22, 'DMSController', 'accessDMSUrl', 'DMS URL', 'To access DMS Url', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(25, 'CIFModificationController', 'accessCIFModification', 'CIF Modification', 'To access CIF Modification', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(26, 'DynamicAPICredentialController', 'accessDynamicAPICredential', 'Dynamic API Credential', 'To access Dynamic API Credential', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(27, 'FeedbackController', 'feedback', 'FeedBack', NULL, 'web', '2024-03-04 13:51:53', '2024-03-04 13:51:53'),
(28, 'CIBackendController', 'astha_api', 'Astha Api', 'Astha Api', 'web', '2024-04-04 08:32:45', '2024-04-04 08:32:45'),
(29, 'IRISApiController', 'iris_api', 'IRIS Api', 'IRIS Api', 'web', '2024-04-04 08:43:15', '2024-04-04 08:43:15'),
(30, 'DivisionsController', 'accessDivisionChecker', 'Access Division Checker', 'Access all type of Division Activity', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(31, 'GroupInfoController', 'accessGroupChecker', 'Access Group Checker', 'Access All', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(32, 'SubgroupInfoController', 'accessSubgroupChecker', 'Access Subgroup Checker', 'Access All', 'web', '2023-06-08 12:12:54', '2023-06-08 12:12:54'),
(33, 'HolidaysController', 'accessHoliday', 'Access Holiday', 'Access All', 'web', '2024-10-03 06:41:28', '2024-10-03 06:41:28'),
(34, 'HolidaysController', 'accessHolidayChecker', 'Access Holiday Checker', 'Access All', 'web', '2024-10-03 06:41:28', '2024-10-03 06:41:28'),
(37, 'SMSEmailsController', 'accessSMSEmail', 'Access SMS Email', 'Access All', 'web', '2024-10-03 07:32:31', '2024-10-03 07:32:31'),
(38, 'SMSEmailsController', 'accessSMSEmailChecker', 'Access SMS Email Checker', 'Access All', 'web', '2024-10-03 07:32:31', '2024-10-03 07:32:31'),
(39, 'ProductTypesController', 'accessProductType', 'Access Product Type', 'Access All', 'web', '2024-10-03 08:09:03', '2024-10-03 08:09:03'),
(40, 'ProductTypesController', 'accessProductTypeChecker', 'Access Product Type Checker', 'Access All', 'web', '2024-10-03 08:09:03', '2024-10-03 08:09:03'),
(41, 'HolidaysController', 'accessWorkingDay', 'Access Working Day', 'Access All', 'web', '2024-10-03 06:41:28', '2024-10-03 06:41:28');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
