-- Add some column for complaint resolve

ALTER TABLE complaint
    ADD COLUMN com_summary TEXT,
ADD COLUMN com_root_cause TEXT,
ADD COLUMN action_taken TEXT,
ADD COLUMN action_date DATE;