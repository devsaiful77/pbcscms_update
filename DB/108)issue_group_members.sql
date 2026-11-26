DROP TABLE IF EXISTS `issue_group_members`;
CREATE TABLE IF NOT EXISTS `issue_group_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `issue_group_id` int DEFAULT NULL,
  `subgroup_info_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `sequence` int DEFAULT NULL,
  `is_touch_point` int DEFAULT NULL,
  `ordering` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

