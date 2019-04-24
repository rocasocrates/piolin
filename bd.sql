          drop table usuarios cascade;

create table usuarios (
  id       bigserial   constraint pk_usuarios primary key,
  usuario  varchar(15) not null constraint uq_usuarios_usuario unique,
  password char(32)    not null,
  email    varchar(75) not null constraint uq_usuarios_email unique
);

create index idx_usuarios_usuario_password on usuarios (usuario, password);

insert into usuarios (usuario, password, email)
values ('manolo', md5('pepe'), 'pepe@pepe.com');
insert into usuarios (usuario, password, email)
values ('maria', md5('juan'), 'juan@juan.com');

drop table tuits cascade;

create table tuits (
  id          bigserial    constraint pk_tuits primary key,
  texto       varchar(140) not null,
  instante    timestamp    default current_timestamp,
  usuarios_id bigserial    not null constraint fk_tuits_usuarios references
                           usuarios(id) on delete cascade on update cascade
);

insert into tuits (texto, instante, usuarios_id)
values ('esto es una prueba', default, 1);
insert into tuits (texto, instante, usuarios_id)
values ('otra prueba', default, 2);

create index idx_tuits_usuario_id on tuits (usuarios_id);

drop table relaciones cascade;	

create table relaciones (
  seguidor_id   bigint    constraint fk_seguidor_usuarios references
                          usuarios (id) on delete cascade on update cascade,
  seguido_id    bigint    constraint fk_seguido_usuarios references
                          usuarios (id) on delete cascade on update cascade,
  desde         timestamp default current_timestamp,	
  constraint pk_relaciones primary key (seguidor_id, seguido_id)
);

create index idx_relaciones_seguido_id on relaciones (seguido_id);

insert into relaciones (seguidor_id, seguido_id)
values (1, 2);

