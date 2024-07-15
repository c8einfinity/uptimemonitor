/*
* log table to store all alerting logs
*/
create table log (
    id integer default 0 not null,
    server_id integer default 0 not null,
    created_at timestamp default CURRENT_TIMESTAMP,
    monitor_type integer default 0 not null,
    status_code integer default 0 not null,
    raw_result varchar(1000) default '',
    primary key (id)
);

/*
* server table to store all server details
* tenant_id is used to identify the tenant. the idea is that servers can be moved between tenants
*/
create table server (
    id integer default 0 not null,
    tenant_id integer default 0 not null,
    server_name varchar(100) default '',
    server_description varchar(255) default '',
    active boolean default true,
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

/*
* server_monitor table to store all server monitor details - this is linked to the server table
*/
create table server_monitor (
    id integer default 0 not null,
    server_id integer default 0 not null,
    monitor_type_id integer default 0 not null,
    ip_address varchar(100) default '',
    domain varchar(100) default '',
    port integer default 0,
    status varchar(100) default '',
    last_run timestamp default CURRENT_TIMESTAMP,
    next_run timestamp default CURRENT_TIMESTAMP,
    last_result varchar(1000) default '',
    last_status_code integer default 0,
    last_run_duration double default 0,
    last_run_raw_result varchar(1000) default '',
    active integer default 0 not null,
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

/*
* monitor_type table to store all monitor types - this is a lookup
*/
create table monitor_type (
    id integer default 0 not null,
    monitor_type varchar(100) default '',
    primary key (id)
);

insert or replace into monitor_type (id, monitor_type) values (1, 'HTTP/HTTPS');
insert or replace into monitor_type (id, monitor_type) values (2, 'Ping');
insert or replace into monitor_type (id, monitor_type) values (3, 'Certificate');
insert or replace into monitor_type (id, monitor_type) values (4, 'Page Speed');

/*
* notification table to store details of where notifications can be sent to
*/
create table notification (
    id integer default 0 not null,
    server_id integer default 0 not null,
    email_alert boolean default true,
    email_address varchar(100) default '',
    threshold integer default 0,
    active boolean default true,
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

/*
* user table to store all user details
*/

create table user (
    id integer default 0 not null,
    username varchar(100) default '',
    password varchar(100) default '',
    email varchar(100) default '',
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

create table user_login (
    id integer default 0 not null,
    date_time timestamp default CURRENT_TIMESTAMP,
    ip_address varchar(100) default '',
    user_agent varchar(100) default '',
    session_id varchar(100) default '',
    status varchar(100) default '',
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

/*
* tenant table to store all tenant details
*/
create table tenant (
    id integer default 0 not null,
    tenant_name varchar(100) default '',
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

/*
* user_tenant table to store the relationship between users and tenants
*/
create table user_tenant (
    id integer default 0 not null,
    user_id integer default 0 not null,
    tenant_id integer default 0 not null,
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);




