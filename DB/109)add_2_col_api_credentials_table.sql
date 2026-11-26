ALTER TABLE api_credential
    ADD COLUMN ci_username VARCHAR(100) NULL DEFAULT NULL AFTER user_password,
    ADD COLUMN ci_password VARCHAR(100) NULL DEFAULT NULL AFTER ci_username;