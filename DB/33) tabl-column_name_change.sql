RENAME TABLE astha_user_sessions TO ci_user_sessions;
ALTER TABLE ci_user_sessions RENAME COLUMN astha_token TO ci_token;


ALTER TABLE settings RENAME COLUMN astha_session_time TO ci_session_time;
ALTER TABLE settings RENAME COLUMN astha_otp_sms_email TO ci_otp_sms_email;


