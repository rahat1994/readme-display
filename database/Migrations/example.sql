id bigint(20) unsigned not null AUTO_INCREMENT primary key,
title varchar(100) not null,
description text null,
created_at timestamp default current_timestamp,
updated_at timestamp null,
deleted_at timestamp null