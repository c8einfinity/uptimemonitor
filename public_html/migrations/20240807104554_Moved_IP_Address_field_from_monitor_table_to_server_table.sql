ALTER TABLE server
ADD COLUMN ip_address varchar(20) default '';

ALTER TABLE server_monitor DROP COLUMN ip_address;