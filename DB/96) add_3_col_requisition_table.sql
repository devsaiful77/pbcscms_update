ALTER TABLE requisition
    ADD COLUMN i_phone VARCHAR(50) NULL AFTER vendors_id,
ADD COLUMN i_address TEXT NULL AFTER i_phone,
ADD COLUMN i_branch_id BIGINT(20) UNSIGNED NULL AFTER i_address;
