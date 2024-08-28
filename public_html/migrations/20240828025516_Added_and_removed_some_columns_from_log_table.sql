ALTER TABLE log DROP COLUMN server_id;
ALTER TABLE log DROP COLUMN monitor_type;
ALTER TABLE log DROP COLUMN server_monitor_id;
ALTER TABLE log DROP COLUMN server_monitor_name;
ALTER TABLE log ADD COLUMN userid integer default 0 not null;