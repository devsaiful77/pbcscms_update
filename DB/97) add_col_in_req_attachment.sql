ALTER TABLE `requisition_attachment`
	ADD COLUMN `is_admin_attach` TINYINT NOT NULL DEFAULT '0' AFTER `attachment_title`;

ALTER TABLE `requisition_attachment`
	ADD COLUMN `product_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `requisition_number`;