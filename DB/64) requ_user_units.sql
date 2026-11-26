ALTER TABLE `user_units`
  ADD `region_id` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `is_unit_head`,
  ADD INDEX (`region_id`);
