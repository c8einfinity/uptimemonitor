create table log (
    id integer default 0 not null,
    tenant_id integer default 0 not null,
    site_id integer default 0 not null,
    created_at timestamp default CURRENT_TIMESTAMP,
    monitor_type integer default 0 not null,
    status_code integer default 0 not null,
    raw_result varchar(1000) default '',
    primary key (id)
);