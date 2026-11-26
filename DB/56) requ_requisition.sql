
DROP TABLE IF EXISTS `requisition`;
COMMIT;
CREATE TABLE `requisition` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`requisition_number` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`type` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
	`specification` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`date` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`created_by` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`access_by` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`access_date` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`subgroup_id` INT NULL DEFAULT NULL,
	`sub_group_info_id` INT NULL DEFAULT NULL,
	`source_subgroup_id` INT NULL DEFAULT NULL,
	`source_sub_group_info_id` INT NULL DEFAULT NULL,
	`region_id` INT NULL DEFAULT NULL,
	`unit_id` INT NULL DEFAULT NULL,
	`source_unit_id` INT NULL DEFAULT NULL,
	`justification` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`remark` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`delivery_date` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`receive_date` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`status` TINYINT NOT NULL DEFAULT '1',
	`vendor_product_map` JSON NULL DEFAULT NULL,
	`vendors_id` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`is_sendback` TINYINT NOT NULL DEFAULT '0',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `requisition_number` (`requisition_number`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

