CREATE TABLE `account_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `short_id` INT NULL COMMENT 'Like 21, 23, 24 ',
    `description` VARCHAR(100) NULL,
    `ac_type` VARCHAR(50) NULL,
    `categ_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Account Category Code',
    `title` VARCHAR(255) NOT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;