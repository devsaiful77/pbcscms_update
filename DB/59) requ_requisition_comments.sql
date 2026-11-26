DROP TABLE IF EXISTS `requisition_comments`;

CREATE TABLE `requisition_comments` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `requisition_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `unit_id` bigint UNSIGNED DEFAULT NULL,
    `group_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `subgroup_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `region_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `isapproved` tinyint NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;