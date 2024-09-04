create table user_role (
    id integer default 0 not null,
    server_id integer default 0 not null,
    user_role_id integer default 0 not null,
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

create table user_role_type (
    id integer default 0 not null,
    user_role_type varchar(100) default '',
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

create table team (
    id integer default 0 not null,
    tenant_id integer default 0 not null,
    team_name varchar(100) default '',
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);

create table user_team (
    id integer default 0 not null,
    user_id integer default 0 not null,
    team_id integer default 0 not null,
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP,
    primary key (id)
);