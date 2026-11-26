CREATE TABLE `outgoing_vendor_email` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`requisition_number` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`vendor_id` BIGINT UNSIGNED NULL DEFAULT NULL,
	`products_id` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`status` TINYINT(1) NOT NULL DEFAULT '0',
	`save_time` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`sent_time` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`repeat_time` TINYINT NOT NULL DEFAULT '0',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;