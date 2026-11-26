DROP TABLE IF EXISTS `requisition_attachment`;

CREATE TABLE `requisition_attachment` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `requisition_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `attachment` varchar(3072) COLLATE utf8mb4_unicode_ci NULL,
    `attachment_title` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;