ALTER TABLE notification DROP COLUMN email_alert;

ALTER TABLE notification ADD COLUMN tenant_id integer default 0 not null;
ALTER TABLE notification ADD COLUMN notificationtype_id integer default 0 not null;
ALTER TABLE notification ADD COLUMN slack_url varchar(255) default '';