CREATE TABLE `requisition_sms_email_templates` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `issue_opening_sms` TEXT NULL,
    `issue_opening_email` TEXT NULL,
    `issue_closing_sms` TEXT NULL,
    `issue_closing_email` TEXT NULL,
    `requisition_approve_sms` TEXT NULL,
    `requisition_approve_email` TEXT NULL,
    `requisition_cancel_sms` TEXT NULL,
    `requisition_cancel_email` TEXT NULL,
    `requisition_sendback_sms` TEXT NULL,
    `requisition_sendback_email` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;