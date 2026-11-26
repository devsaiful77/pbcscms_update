LOAD DATA INFILE 'E:/information/Prime/BPID/records.csv' INTO TABLE `temp_staging_records` FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 LINES (`bp_id`, `customer_name`, `account_number`, `SIF_Number`, `mobile_number`, `email_address`, `branch_name`);



DROP PROCEDURE IF EXISTS `sp_process_bpid_migration`;

DELIMITER $$

CREATE PROCEDURE `sp_process_bpid_migration`()
BEGIN
    -- loop variable
    DECLARE done INT DEFAULT FALSE;
    
    -- staging table variable
    DECLARE v_bp_id VARCHAR(50);
    DECLARE v_customer_name VARCHAR(150);
    DECLARE v_account_number VARCHAR(50);
    DECLARE v_SIF_Number VARCHAR(50);
    DECLARE v_mobile_number VARCHAR(50);
    DECLARE v_email_address VARCHAR(100);
    DECLARE v_branch_name VARCHAR(100);

    -- prosses variable
    DECLARE v_seq_id BIGINT;
    DECLARE v_ref_no VARCHAR(50);
    DECLARE v_now DATETIME;
    DECLARE v_time_int INT;
    DECLARE v_time_ext TIME;
    DECLARE v_extra_json JSON;

    -- temp_staging_records cursor
    DECLARE cur_records CURSOR FOR 
        SELECT `bp_id`, `customer_name`, `account_number`, `SIF_Number`, `mobile_number`, `email_address`, `branch_name`
        FROM `temp_staging_records`;

    -- loop ending
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- 1. sequences_sr table reset
    IF EXISTS (SELECT 1 FROM `sequences_sr` WHERE DATE(`created_at`) < CURDATE() LIMIT 1) THEN
        DELETE FROM `sequences_sr`;
        ALTER TABLE `sequences_sr` AUTO_INCREMENT = 1;
    END IF;

    -- cursor open
    OPEN cur_records;

    read_loop: LOOP
        FETCH cur_records INTO 
            v_bp_id, v_customer_name, v_account_number, v_SIF_Number, v_mobile_number, v_email_address, v_branch_name;

        IF done THEN
            LEAVE read_loop;
        END IF;

        -- tranjection start
        START TRANSACTION;

        SET v_now = NOW();
        SET v_time_int = UNIX_TIMESTAMP(v_now);
        SET v_time_ext = TIME(v_now);

        -- 2. sequences create and ref no. generate
        INSERT INTO `sequences_sr` (`created_at`, `updated_at`) VALUES (v_now, v_now);
        SET v_seq_id = LAST_INSERT_ID();
        SET v_ref_no = CONCAT('S', DATE_FORMAT(v_now, '%y%m%d'), 'AC', LPAD(v_seq_id, 6, '0'));

        -- 3. reference insert
        INSERT INTO `reference` (
            `reference_number`, `date`, `created_by`, `account_type`, `subgroup_id`, `sub_group_info_id`, 
            `unit_id`, `issue_id`, `is_tara`, `status`, `segment_priority`, `form_status`, 
            `api_status`, `iris_api_status`, `failed_api`, `failed_retry`, `memo`, `issues_from`, 
            `access_by`, `access_date`, `created_at`, `updated_at`
        ) VALUES (
            v_ref_no, v_time_int, 'mm062802', 2, 198, 562, 
            2, 1450, 0, 47, 0, 11, 
            0, 0, NULL, 0, NULL, 'wform', 
            'enam.hasan', v_time_int, v_now, v_now
        );

        -- 4. bp_ids insert
        INSERT INTO `bp_ids` (
            `bp_id`, `account_number`, `reference_number`, `branch_name`, `account_title`, 
            `contact_no_1`, `email_1`, `created_at`, `updated_at`
        ) VALUES (
            v_bp_id, v_account_number, v_ref_no, v_branch_name, v_customer_name, 
            v_mobile_number, v_email_address, v_now, v_now
        );

        -- 5. comments insert
        INSERT INTO `comments` (
            `reference_number`, `comments`, `time`, `user_id`, `unit_id`, `group_id`, 
            `subgroup_id`, `action`, `duration_in_minutes`, `isapproved`, `issendback`, 
            `ip`, `created_at`, `updated_at`
        ) VALUES (
            v_ref_no, 'this is a existing BPID. Created by Script.', v_time_int, 'enam.hasan', 2, 198, 
            562, 'Close', 0.00, 0, 0, 
            '127.0.0.1', v_now, v_now
        );

        -- 6. w_form insert
        INSERT INTO `w_form` (
            `reference_number`, `account_number`, `customer_name`, `mobile_number`, `email_address`, 
            `product_type`, `time_and_ext`, `SIF_Number`, `w_form_type`, `acc_opening_branch`, 
            `balance`, `created_at`, `updated_at`
        ) VALUES (
            v_ref_no, v_account_number, v_customer_name, v_mobile_number, v_email_address, 
            2, v_time_ext, v_SIF_Number, 1450, v_branch_name, 
            0.00, v_now, v_now
        );

        -- 7. extra_field JSON create
        SET v_extra_json = JSON_ARRAY(
            JSON_OBJECT('BP Type Check:', 'Individual'),
            JSON_OBJECT('Residency', 'Resident'),
            JSON_OBJECT('Applicant Type', 'First'),
            JSON_OBJECT('Name of the Account:', COALESCE(v_customer_name, '')),
            JSON_OBJECT('First Applicant Gender', ''),
            JSON_OBJECT('First Applicant Date of Birth:', ''),
            JSON_OBJECT('First Applicant Mother\'s Name', ''),
            JSON_OBJECT('First Applicant Father\'s Name:', ''),
            JSON_OBJECT('First Applicant NID/Passport:', ''),
            JSON_OBJECT('First Applicant e-TIN No. (if any)', ''),
            JSON_OBJECT('First Applicant Occupation:', 'Profession'),
            JSON_OBJECT('Second Applicant Gender', ''),
            JSON_OBJECT('Second Applicant Date of Birth:', ''),
            JSON_OBJECT('Second Applicant Mother\'s Name', ''),
            JSON_OBJECT('Second Applicant Father\'s Name:', ''),
            JSON_OBJECT('Second Applicant NID/Passport::', ''),
            JSON_OBJECT('Second Applicant e-TIN No. (if any)', ''),
            JSON_OBJECT('Second Applicant Occupation:', ''),
            JSON_OBJECT('Third Applicant Gender', ''),
            JSON_OBJECT('Third Applicant Date of Birth:', ''),
            JSON_OBJECT('Third Applicant Mother\'s Name', ''),
            JSON_OBJECT('Third Applicant Father\'s Name:', ''),
            JSON_OBJECT('Third Applicant NID/Passport:', ''),
            JSON_OBJECT('Third Applicant e-TIN No. (if any)', ''),
            JSON_OBJECT('Third Applicant Occupation:', ''),
            JSON_OBJECT('Fourth Applicant Gender', ''),
            JSON_OBJECT('Fourth Applicant Date of Birth:', ''),
            JSON_OBJECT('Fourth Applicant Mother\'s Name', ''),
            JSON_OBJECT('Fourth Applicant Father\'s Name:', ''),
            JSON_OBJECT('Fourth Applicant NID/Passport:', ''),
            JSON_OBJECT('Fourth Applicant e-TIN No. (if any)', ''),
            JSON_OBJECT('Fourth Applicant Occupation:', ''),
            JSON_OBJECT('Present Address:', ''),
            JSON_OBJECT('Permanent Address:', ''),
            JSON_OBJECT('Phone No:', COALESCE(v_mobile_number, '')),
            JSON_OBJECT('Mobile No:', COALESCE(v_mobile_number, '')),
            JSON_OBJECT('Email:', COALESCE(v_email_address, '')),
            JSON_OBJECT('Bank Name:', 'Prime Bank PLC.'),
            JSON_OBJECT('Branch Name:', COALESCE(v_branch_name, '')),
            JSON_OBJECT('Account Number:', COALESCE(v_account_number, '')),
            JSON_OBJECT('Account Type:', 'CONSUMER'),
            JSON_OBJECT('Routing No:', ''),
            JSON_OBJECT('First Nominee Name', ''),
            JSON_OBJECT('First Nominee NID/Passport/Birth No:', ''),
            JSON_OBJECT('First Nominee Address', ''),
            JSON_OBJECT('First Nominee Relation with Account Holder:', ''),
            JSON_OBJECT('First Nominee DOB', ''),
            JSON_OBJECT('First Nominee % Payable', '50'),
            JSON_OBJECT('Second Nominee Name', ''),
            JSON_OBJECT('Second Nominee NID/Passport/Birth No:', ''),
            JSON_OBJECT('Second Nominee Address', ''),
            JSON_OBJECT('Second Nominee Relation with Account Holder:', ''),
            JSON_OBJECT('Second Nominee DOB', ''),
            JSON_OBJECT('Second Nominee % Payable', ''),
            JSON_OBJECT('Third Nominee Name', ''),
            JSON_OBJECT('Third Nominee NID/Passport/Birth No:', ''),
            JSON_OBJECT('Third Nominee Address:', ''),
            JSON_OBJECT('Third Nominee Relation with Account Holder:', ''),
            JSON_OBJECT('Third Nominee DOB:', ''),
            JSON_OBJECT('Third Nominee % Payable', ''),
            JSON_OBJECT('Fourth Nominee Name', ''),
            JSON_OBJECT('Fourth Nominee NID/Passport/Birth No:', ''),
            JSON_OBJECT('Fourth Nominee Address', ''),
            JSON_OBJECT('Fourth Nominee Relation with Account Holder:', ''),
            JSON_OBJECT('Fourth Nominee DOB', ''),
            JSON_OBJECT('Fourth Nominee % Payable', ''),
            JSON_OBJECT('First Applicant Name:', COALESCE(v_customer_name, '')),
            JSON_OBJECT('Second Applicant Name:', ''),
            JSON_OBJECT('Third Applicant Name:', ''),
            JSON_OBJECT('Four Applicant Name:', ''),
            JSON_OBJECT('First Applicant Contact No:', COALESCE(v_mobile_number, '')),
            JSON_OBJECT('First Applicant Email:', COALESCE(v_email_address, '')),
            JSON_OBJECT('Second Applicant Contact No:', ''),
            JSON_OBJECT('Second Applicant Email:', ''),
            JSON_OBJECT('Third Applicant Contact No:', ''),
            JSON_OBJECT('Third Applicant Email:', ''),
            JSON_OBJECT('Four Applicant Contact No:', ''),
            JSON_OBJECT('Four Applicant Email:', '')
        );

        -- 8. w_form_type insert
        INSERT INTO `w_form_type` (`reference_number`, `extra_field`, `check_list`) 
        VALUES (v_ref_no, v_extra_json, NULL);

        COMMIT;
    END LOOP;

    CLOSE cur_records;
END$$

DELIMITER ;




CALL sp_process_bpid_migration();