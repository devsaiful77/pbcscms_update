DROP TABLE IF EXISTS `branch_code_tmps`;

CREATE TABLE `branch_code_tmps` (
    `id` bigint UNSIGNED NOT NULL,
    `company_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `br_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `branch_name` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `mnemonic` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `region` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `action` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `status` tinyint(1) DEFAULT '0',
    `form_status` int NOT NULL,
    `created_by` int DEFAULT NULL,
    `master_id` int DEFAULT NULL,
    `modified_by` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `comments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `branch_code_tmps` ADD PRIMARY KEY (`id`);

ALTER TABLE `branch_code_tmps`
MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 8;