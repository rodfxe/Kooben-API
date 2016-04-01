### BEGINS PROJECT

# table for sessions.
drop table if exists nuc_sessions;
create table nuc_sessions(
    id int not null auto_increment,
    hash varchar( 255 ) not null,
    username varchar( 255 ) not null,

    createdAt datetime,
    active enum( 'true', 'false' ) default 'true',

    primary key ( id )
);

# table for kardex.
drop table if exists nuc_kardex;
create table nuc_kardex(
	id int not null auto_increment,

	rowId int not null,
	sessionId int not null,

	tableName varchar( 255 ) not null,
	operation enum( 'insert', 'update', 'delete' ),
	affectedAt datetime,

	createdAt datetime,
    active enum( 'true', 'false' ) default 'true',

    primary key ( id )
);

# cmt_insumo
alter table cmt_insumo add column eClasificacion enum( 'basica', 'normal' ) not null default 'normal' after IdTipoInsumo;
update cmt_insumo set eClasificacion = 'basica' where IdClasificacion = 1;
alter table cmt_insumo drop column IdClasificacion;
drop table if exists cmt_clasificaciones;


# cmt_insumoprecio
delete from cmt_insumoprecio where IdInsumoPrecio = 0;
alter table cmt_insumoprecio change column IdInsumoPrecio IdInsumoPrecio int(11) not null auto_increment;

# cmt_recetapartida
alter table cmt_recetapartida change column IdRecetaPartida IdRecetaPartida int(11) not null auto_increment;
# cmt_insumo
alter table cmt_insumo change column IdInsumo IdInsumo int(11) not null auto_increment;

# cmt_insumosku
delete from cmt_insumosku where IdEmpresa = 0;
# cmt_insumoprecio
delete from cmt_insumoprecio where iIdEmpresa = 0;

# nuc_empresas
delete from nuc_empresas where iIdEmpresa = 0;
alter table nuc_empresas change column iIdEmpresa iIdEmpresa int(11) not null auto_increment;

# cmt_marca
alter table cmt_marca change column IdMarca IdMarca int(11) not null auto_increment;
# cmt_presentacion
alter table cmt_presentacion change column IdPresentacion IdPresentacion int(11) not null auto_increment;


### 2015-12-18

# nuc_kardex
alter table nuc_kardex drop column createdAt;
alter table nuc_kardex change operation operation enum( 'insert', 'update', 'delete', 'physical-erased' ) not null;

# nuc_sessions;
alter table nuc_sessions add column application enum( 'web', 'windows-desktop', 'ios', 'android', 'windows-phone', 'os-x' ) after createdAt;
alter table nuc_sessions add column type enum( 'free', 'log' ) default 'free' after application;
alter table nuc_sessions add column userid int default null after type;




### 2015-12-18: 15:00pm




# cmt_menu
alter table cmt_menu add column IdSession int default null after IdEmbarcacion;
alter table `cmt_menu` change column `IdMenu` `IdMenu` int(11) NOT NULL AUTO_INCREMENT;

# cmt_registroconsumodatos
delete from cmt_registroconsumodatos;
alter table cmt_registroconsumodatos CHANGE COLUMN IdRegistroConsumoDatos IdRegistroConsumoDatos int(11) not null auto_increment;
ALTER TABLE cmt_registroconsumodatos
    DROP FOREIGN KEY `cmt_registroconsumodatos_fk`,
    DROP FOREIGN KEY `cmt_registroconsumodatos_fk1`;
ALTER TABLE cmt_registroconsumodatos
    ADD CONSTRAINT `cmt_registroconsumodatos_fk` FOREIGN KEY (`IdPeriodoConsumo`)
        REFERENCES cmt_periodoconsumo (`IdPeriodoConsumo`)
            ON UPDATE CASCADE
            ON DELETE CASCADE,
    ADD CONSTRAINT `cmt_registroconsumodatos_fk1` FOREIGN KEY (`IdRegistroConsumo`)
        REFERENCES cmt_registroconsumo (`IdRegistroConsumo`)
            ON UPDATE CASCADE
            ON DELETE CASCADE,
    ADD CONSTRAINT `cmt_registroconsumodatos_fk2` FOREIGN KEY (`IdReceta`)
        REFERENCES cmt_receta (`IdReceta`)
            ON UPDATE CASCADE
            ON DELETE CASCADE;



# cmt_registroconsumo
delete from cmt_registroconsumo;
alter table cmt_registroconsumo drop foreign key `cmt_registroconsumo_fk`;
alter table cmt_registroconsumo drop column IdEmbarcacion;
alter table cmt_registroconsumo add column IdMenu int not null after IdRegistroConsumo;
alter table cmt_registroconsumo change column IdRegistroConsumo IdRegistroConsumo int(11) not null auto_increment;
alter table cmt_registroconsumo add constraint `cmt_registroconsumo_menu_fk` FOREIGN KEY (`IdMenu`) REFERENCES `cmt_menu` (`IdMenu`)   ON UPDATE CASCADE ON DELETE CASCADE;
alter table cmt_registroconsumo DROP INDEX `Unico`;



# 2015-01-11
# nuc_sessions
ALTER TABLE nuc_sessions CHANGE COLUMN `userid` `userid` varchar(255) DEFAULT NULL;
ALTER TABLE nuc_sessions CHANGE COLUMN `username` `username` varchar(255);