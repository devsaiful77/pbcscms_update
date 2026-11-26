
DROP TABLE IF EXISTS `requisition_categories`;
CREATE TABLE IF NOT EXISTS `requisition_categories` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `category_name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_name_unique` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

