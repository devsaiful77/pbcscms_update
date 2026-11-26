SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `issue_workflows_tmp` (
  `issue_workflow_id` bigint UNSIGNED NOT NULL,
  `flow_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_id` bigint UNSIGNED NOT NULL,
  `log` int DEFAULT '0',
  `execute` int DEFAULT '0',
  `send_back` int DEFAULT '0',
  `cant_reach_to_customer` int DEFAULT '0',
  `complain_sla_time` int DEFAULT NULL,
  `action` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `master_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  `ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `issue_workflows_tmp`
  ADD PRIMARY KEY (`issue_workflow_id`),
  ADD KEY `issue_workflows_issue_id_foreign` (`issue_id`),
  ADD KEY `flow_type` (`flow_type`);

ALTER TABLE `issue_workflows_tmp`
  MODIFY `issue_workflow_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

COMMIT;

ALTER TABLE issue_workflows_tmp
ADD touch_group_old JSON AFTER complain_sla_time,
ADD non_touch_group_old JSON AFTER touch_group_old;

