-- ----------------------------------------------------------------------------------
-- Удаление старых версий таблиц данных
-- ----------------------------------------------------------------------------------

drop table if exists session_storage;
drop table if exists value_storage;
drop table if exists migrations;

-- ----------------------------------------------------------------------------------
-- Добавление расширений
-- ----------------------------------------------------------------------------------
-- create extension if not exists "uuid-ossp";           -- (from postgresql-contrib)

-- ----------------------------------------------------------------------------------
-- Информация о миграциях
-- ----------------------------------------------------------------------------------
create table migrations (

    migration_version   varchar(20)         default '0.0.0'             not null    ,
    migration_time      timestamp           default now()               not null    ,
    migration_comment   text                                                        ,

    constraint pk_migrations primary key (migration_version)
);

-- Комментарии ----------------------------------------------------------------------
comment on table  migrations                    is 'Информация о миграциях';
comment on column migrations.migration_version  is 'Номер версии';
comment on column migrations.migration_time     is 'Дата и время миграции';
comment on column migrations.migration_comment  is 'Комментарий';

-- Инициализация --------------------------------------------------------------------
insert into migrations values ('7.0.0', now(), '');

-- ----------------------------------------------------------------------------------
-- Хранилище значений
-- ----------------------------------------------------------------------------------
create table value_storage (

    value_storage_name  varchar(120)                                    not null    ,
    value_storage_data  text                                                        ,
    value_storage_valid timestamp           default null                            ,

    constraint pk_value_storage primary key (value_storage_name)
);
-- Комментарии ----------------------------------------------------------------------
comment on table  value_storage                     is 'Хранилище значений';
comment on column value_storage.value_storage_name  is 'Уникальный идентификатор';
comment on column value_storage.value_storage_data  is 'Значение';
comment on column value_storage.value_storage_valid is 'Действительно до';

-- ----------------------------------------------------------------------------------
-- Хранилище сессий пользователей
-- ----------------------------------------------------------------------------------
create table session_storage (

    session_storage_id      uuid                                        not null    ,
    session_storage_time    timestamp       default now()                           ,
    session_storage_data    text                                                    ,

    constraint pk_session_storage primary key (session_storage_id)
);
-- Комментарии ----------------------------------------------------------------------
comment on table  session_storage                      is 'Хранилище сессий пользователей';
comment on column session_storage.session_storage_id   is 'Уникальный идентификатор';
comment on column session_storage.session_storage_time is 'Дата и время записи';
comment on column session_storage.session_storage_data is 'Значение';
