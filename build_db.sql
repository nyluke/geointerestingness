create table t_geo (
	country		varchar(255)
);

alter table t_geo add constraint ct_country_geo unique ( country ) ;

create table t_photo (
	id		int not null primary key,
	country		varchar(255)
);

alter table t_photo add constraint fk_country_photo_geo foreign key ( country ) references t_geo ( country );

create index idx_country_photo on t_photo (
	country
);