ALTER TABLE `sms_email_templates`
ADD `requisition_approve_email` TEXT NULL DEFAULT NULL AFTER `unit_head_tat_breach_mail`,
ADD `requisition_sendback_email` TEXT NULL DEFAULT NULL AFTER `requisition_approve_email`,
ADD `requisition_close_email` TEXT NULL DEFAULT NULL AFTER `requisition_sendback_email`,
ADD `requisition_cancel_email` TEXT NULL DEFAULT NULL AFTER `requisition_close_email`;