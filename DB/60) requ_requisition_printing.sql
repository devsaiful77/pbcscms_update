DROP TABLE IF EXISTS `requisition_printing`;

CREATE TABLE `requisition_printing` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `requisition_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `product_id` bigint UNSIGNED DEFAULT NULL,
    `quantity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `approve_qty` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
    `status` tinyint NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;