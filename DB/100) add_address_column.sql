ALTER TABLE `group_info_tmps`
    ADD `address` VARCHAR(255) COLLATE utf8mb4_general_ci NULL AFTER `description`;

ALTER TABLE `group_info`
    ADD `address` VARCHAR(255) COLLATE utf8mb4_general_ci NULL AFTER `description`;


ALTER TABLE `subgroup_info_tmps`
    ADD `address` VARCHAR(255) COLLATE utf8mb4_general_ci NULL AFTER `description`;


ALTER TABLE `subgroup_info`
    ADD `address` VARCHAR(255) COLLATE utf8mb4_general_ci NULL AFTER `description`;