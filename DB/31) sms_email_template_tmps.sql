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
-- Table structure for table `sms_email_template_tmps`
--

DROP TABLE IF EXISTS `sms_email_template_tmps`;
CREATE TABLE IF NOT EXISTS `sms_email_template_tmps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `issue_opening_sms_wform` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issue_opening_sms_complaint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issue_opening_email_wform` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issue_opening_email_complaint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issue_closing_sms_wform` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issue_closing_sms_complaint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issue_closing_email_wform` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issue_closing_email_complaint` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `otp_sms` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `send_back_sms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `send_back_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `send_back_auto_sms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `send_back_auto_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `send_back_closed_sms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `send_back_closed_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unreachable_cust_sms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unreachable_cust_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `non_cust_sms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `non_cust_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `escalation_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unit_head_tat_breach_mail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `form_status` int NOT NULL,
  `modified_by` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `master_id` int DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_notify_sms` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_notify_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sms_email_template_tmps`
--

INSERT INTO `sms_email_template_tmps` (`id`, `issue_opening_sms_wform`, `issue_opening_sms_complaint`, `issue_opening_email_wform`, `issue_opening_email_complaint`, `issue_closing_sms_wform`, `issue_closing_sms_complaint`, `issue_closing_email_wform`, `issue_closing_email_complaint`, `otp_sms`, `otp_email`, `send_back_sms`, `send_back_email`, `send_back_auto_sms`, `send_back_auto_email`, `send_back_closed_sms`, `send_back_closed_email`, `unreachable_cust_sms`, `unreachable_cust_email`, `non_cust_sms`, `non_cust_email`, `escalation_email`, `unit_head_tat_breach_mail`, `action`, `status`, `form_status`, `modified_by`, `master_id`, `comments`, `created_by`, `updated_by`, `ip`, `user_notify_sms`, `user_notify_email`, `created_at`, `updated_at`) VALUES
(17, 'This is a test message. Your {form_request} request has been received successfully. Ticket no {reference_no}. Query: PRIME UAT Team.tttttttt', 'This is a test message. Your {form_request} complaint has been received successfully. Ticket no {reference_no}. Query: PRIME UAT Team.', 'This is a test message.\r\nYour {form_request} request has been received successfully. Ticket No {reference_no}. Query: 16. Thank you for staying with Prime Bank.\r\nRegards,\r\nPRIME UAT Team', 'This is a test message.. \r\nYour {form_request} complaint has been received successfully. Ticket No {reference_no}. Thank you for informing us. \r\nRegards,\r\nPRIME UAT Team', 'This is a test message.\r\nYour {form_request} request has been resolved successfully. Ticket no {reference_no}. Query: PRIME UAT Team.', 'This is test message.  Your {form_request} complaint has been solved. Ticket no {reference_no}. Query: PRIME UAT Team.', 'This is a test message.\r\nYour {form_request} request has been resolved successfully. Ticket no {reference_no}. Query: PRIME  UAT Team. \r\nRegards,\r\nPRIME UAT Team', 'This is a test message.\r\nYour {form_request} complaint has been solved. Ticket no {reference_no}. Query: PRIME UAT Team.\r\n\r\nRegards,\r\nPRIME UAT Team', 'Use OTP: {otp_code} to proceed with service/complaint lodgement. This OTP will be valid for 3 minutes. Please do not shareÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢Ã', 'Dear Customer,\r\n \r\nYour OTP is {otp_code} to proceed with your current Internet Banking activity and will be valid for next 3 minutes. For security, please do not share this with anyone. \r\n\r\nFor queries: call 16 (Local); +8802556, +8802, +8809 (Overseas).\r\n\r\nSincerely, \r\nUAT-BRACÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â BankÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â Limited', 'This is a test message. Your service Ticket No {reference_no} is being processed. \r\nSend Back Reason is {Send_back_reason}\r\nThank you for your assistance in resolving the issue. Query: PRIME UAT Team.', 'This is a test message.\r\nYour service Ticket No {reference_no} is being processed. Thank you for your assistance to resolve the issue {form_request}. \r\nSend Back Reason is {Send_back_reason}\r\nQuery: PRIME UAT Team.\r\nThank you for staying with Prime Bank.\r\n\r\nRegards,\r\nPRIME UAT Team', 'This is a test message. Your service Ticket No {reference_no} is being processed. Thank you for your assistance to resolve the issue. Query: PRIME UAT Team.', 'This is a test message. Your service Ticket No {reference_no} is being processed. Thank you for your assistance to resolve the issue {form_request}. Query: PRIME UAT Team. Thank you for staying with Prime Bank.  Regards, PRIME UAT Team', '', '', 'This is a test message. We would like to communicate with you to resolve your {reference_no} issue of {form_request}. Query: PRIME UAT Team.', 'This is a test message.\r\nThank you for calling us. We would like to communicate with you to resolve your {reference_no} issue of   {form_request}. Query: 16221. Thank you for staying with Prime Bank.\r\n\r\nRegards,\r\nPRIME UAT Team', 'This is a test message. Thank you from Prime Bank. We want to continue to serve you and make your experience even better. Query No: {reference_no}', 'This is a test message. Thank you from Prime Bank. We want to continue to serve you and make your experience even better.. Query No: {reference_no}', 'This is a test message.\r\nEscalation eMail has been generated for issue {form_request} and the Ticket No: {reference_no} and group name is {group_name}. Please have a look.\r\nRegards,\r\nPrime UAT Team', NULL, 'Edit', 2, 7, NULL, 1, 'rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr', '1', 1, '::1', NULL, NULL, '2024-10-17 11:26:14', '2024-10-17 11:37:47');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
