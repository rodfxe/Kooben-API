Planeación de recetas
=====================


# Planeacion
El objetivo de la planeación de recetas es proporcionarle al usuario
una interfaz por la que pueda organizar sus recetas, cotizar el precio
de la planeación y poder efectuar la compra de dicha planeación.


Dicha estructurá estará compuesta de la siguiente manera.

Existirá un catalogo de planeaciones el cual estará ligado al usuario
que haya creado la planeacion.
El periodo de una planeación solo durará `una semana`.


La planeacion puede tener un titulo o nombre para que el usuario pueda
diferencias de sus otras planeaciones, por ejemplo: `reunion`,
`fiesta de mi hija`, etc.

Existirá otra tabla la cual estará ligada a la anterior mencionada, esta
almacenará los días del periodo creado y especificará una cantidad de
personas para cada día.


Por ultimo está ultima tabla almacenará las recetas que se vayan
especificando para los días.



## Scripts
```mysql
drop table if exists cmt_planeacion;
create table cmt_planeacion (
	id int not null auto_increment primary key,
	idUsuario int not null,
	titulo varchar( 255 ) not null,
	desde date not null,
	hasta date not null,

	activo tinyint default 1
);


drop table cmt_planeaciondias;
create table cmt_planeaciondias (
	id int not null auto_increment primary key,
	planeacionId int not null,
	fecha date,
	personas int
);



drop table if exists cmt_planeacionrecetas;
create table cmt_planeacionrecetas (
	id int not null auto_increment primary key,
	diaId int not null,
	recetaId int not null,
	periodo tinyint
);

```
