-- Alter the `complaint` table
ALTER TABLE `complaint`
ADD `branch_code` VARCHAR(200) NULL AFTER `acc_opening_branch`,
ADD `communication` TEXT NULL AFTER `branch_code`,
ADD `customer_nid` VARCHAR(50) NULL AFTER `communication`,
ADD `passpor_number` VARCHAR(50) NULL AFTER `customer_nid`;

-- Alter the `w_form` table
ALTER TABLE `w_form`
ADD `branch_code` VARCHAR(200) NULL AFTER `acc_opening_branch`,
ADD `communication` TEXT NULL AFTER `branch_code`,
ADD `customer_nid` VARCHAR(50) NULL AFTER `communication`,
ADD `passpor_number` VARCHAR(50) NULL AFTER `customer_nid`;
