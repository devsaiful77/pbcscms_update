DROP TABLE IF EXISTS `issue_groups`;
CREATE TABLE IF NOT EXISTS `issue_groups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_item_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




INSERT INTO `permissions` (`id`, `controller_name`, `name`, `display_name`, `description`, `guard_name`, `module`, `created_at`, `updated_at`) VALUES (NULL, 'IssueGroupController', 'accessIssueGroupChecker', 'Access Issue Group Checker', 'Access all type of Issue Group Checker Activity', 'web', '1', '2023-06-08 06:12:54', '2023-06-08 06:12:54');

INSERT INTO `permissions` (`id`, `controller_name`, `name`, `display_name`, `description`, `guard_name`, `module`, `created_at`, `updated_at`) VALUES (NULL, 'IssueGroupController', 'accessIssueGroup', 'Access Issue Group', 'Access all type of Issue Group Activity', 'web', '1', '2023-06-08 06:12:54', '2023-06-08 06:12:54');

