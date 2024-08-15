/*
* notification_type table to store all notification types - this is a lookup
*/
create table notification_type (
    id integer default 0 not null,
    notification_type varchar(100) default '',
    primary key (id)
);

insert or replace into notification_type (id, notification_type) values (1, 'E-Mail');
insert or replace into notification_type (id, notification_type) values (2, 'Slack');