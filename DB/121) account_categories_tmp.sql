DROP TABLE IF EXISTS `account_categories_tmp`;

CREATE TABLE `account_categories_tmp` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `short_id` int NULL,
    `description` varchar(100) DEFAULT NULL,
    `ac_type` varchar(50) DEFAULT NULL,
    `categ_code` varchar(50) DEFAULT NULL,
    `title` varchar(255) DEFAULT NULL,
    `action` varchar(250) DEFAULT NULL,
    `status` tinyint(1) DEFAULT '0',
    `form_status` int NOT NULL DEFAULT '0',
    `created_by` int DEFAULT NULL,
    `master_id` int DEFAULT NULL,
    `modified_by` varchar(30) DEFAULT NULL,
    `ip` varchar(30) DEFAULT NULL,
    `comments` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
