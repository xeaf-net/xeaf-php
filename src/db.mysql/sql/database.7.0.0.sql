-- ----------------------------------------------------------------------------------
-- Удаление старых версий таблиц данных
-- ----------------------------------------------------------------------------------

drop table if exists session_storage;
drop table if exists value_storage;
drop table if exists migrations;

-- ------------------------------------------------------------------------------------------------------
-- Информация о миграциях
-- ------------------------------------------------------------------------------------------------------
create table migrations (

    migration_version       varchar(20) default '0.0.0' not null    comment 'Номер версии'              ,
    migration_time          timestamp   default now()   not null    comment 'Дата и время миграции'     ,
    migration_comment       text                                    comment 'Комментарий'               ,

    constraint pk_migrations primary key (migration_version)

) engine = 'InnoDb' comment 'Информация о миграциях';

-- Инициализация ----------------------------------------------------------------------------------------
insert into migrations values ('7.0.0', now(), '');

-- ------------------------------------------------------------------------------------------------------
-- Хранилище значений
-- ------------------------------------------------------------------------------------------------------
create table value_storage (

    value_storage_name      varchar(120)                not null    comment 'Уникальный идентификатор'  ,
    value_storage_data      text                                    comment 'Значение'                  ,
    value_storage_valid     timestamp                               comment 'Действительно до'          ,

    constraint pk_value_storage primary key (value_storage_name)

) engine = 'InnoDb' comment = 'Хранилище значений';

-- ------------------------------------------------------------------------------------------------------
-- Хранилище сессий пользователей
-- ------------------------------------------------------------------------------------------------------
create table session_storage (

    session_storage_id      char(36)                    not null    comment 'Уникальный идентификатор'  ,
    session_storage_time    timestamp   default now()               comment 'Дата и время записи'       ,
    session_storage_data    text                                    comment 'Значение'                  ,

    constraint pk_session_storage primary key (session_storage_id)

) engine = 'InnoDb' comment = 'Хранилище сессий пользователей';

