ALTER TABLE `divisions_tmp` ADD COLUMN `form_status` integer NOT NULL AFTER `status`;
ALTER TABLE `departments_tmp` ADD COLUMN `form_status` integer NOT NULL AFTER `status`;
ALTER TABLE `group_info_tmps` ADD COLUMN `form_status` integer NOT NULL AFTER `status`;
ALTER TABLE `subgroup_info_tmps` ADD COLUMN `form_status` integer NOT NULL AFTER `status`;
ALTER TABLE `issue_categories_tmp` ADD COLUMN `form_status` integer NOT NULL AFTER `status`;
ALTER TABLE `issue_workflows_tmp` ADD COLUMN `form_status` integer NOT NULL AFTER `status`;