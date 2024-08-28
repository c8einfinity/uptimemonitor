ALTER TABLE log ADD COLUMN server_id integer default 0 not null;
ALTER TABLE log ADD COLUMN monitor_type varchar(30) default '';
ALTER TABLE log DROP COLUMN userid;