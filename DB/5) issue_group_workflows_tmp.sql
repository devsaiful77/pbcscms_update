SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `issue_group_workflows_tmp` (
  `issue_group_workflow_id` bigint UNSIGNED NOT NULL,
  `issue_workflow_id` bigint UNSIGNED NOT NULL,
  `group_info_id` bigint UNSIGNED NOT NULL,
  `touch_maker` int DEFAULT '1',
  `touch_checker` int DEFAULT '0',
  `hold_maker` int DEFAULT '0',
  `hold_checker` int DEFAULT '0',
  `sla_maker` int DEFAULT '0',
  `sla_checker` int DEFAULT '0',
  `attach_maker` int DEFAULT '0',
  `attach_maker_item` int DEFAULT '0',
  `attach_checker` int DEFAULT '0',
  `attach_checker_item` int DEFAULT '0',
  `is_touch_point` tinyint(1) DEFAULT '0',
  `order_by` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `issue_group_workflows_tmp`
  ADD PRIMARY KEY (`issue_group_workflow_id`),
  ADD KEY `issue_group_workflows_issue_workflow_id_foreign` (`issue_workflow_id`),
  ADD KEY `issue_group_workflows_group_info_id_foreign` (`group_info_id`);


ALTER TABLE `issue_group_workflows_tmp`
  MODIFY `issue_group_workflow_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;