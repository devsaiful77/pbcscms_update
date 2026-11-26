ALTER TABLE `requisition`
ADD `delivery_date` VARCHAR(191) NULL DEFAULT NULL AFTER `remark`,
ADD `receive_date` VARCHAR(191) NULL DEFAULT NULL AFTER `delivery_date`;