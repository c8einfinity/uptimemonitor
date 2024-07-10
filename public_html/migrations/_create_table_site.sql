create table site (
	batch_size varchar(1000),
	batch_count varchar(1000),
	batch_started varchar(1000),
	sotf_delete varchar(1000),
	id integer not null,
	tenant_id varchar(1000),
	site_name varchar(1000),
	site_address varchar(1000),
   primary key (id)
)