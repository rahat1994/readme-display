id bigint(20) unsigned not null AUTO_INCREMENT primary key,
name varchar(100) not null,
slug varchar(100) not null,
readme_contents longtext null,
created_at timestamp default current_timestamp,
updated_at timestamp null,
deleted_at timestamp null