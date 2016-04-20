select
	planeacion.id,
	planeacion.titulo,
	planeacion.idUsuario,
	planeacion.desde,
	planeacion.hasta

from cmt_planeacion as planeacion

where
	( :id = -1 or planeacion.id = :id ) and
	( :usuario = -1 or planeacion.idUsuario = :usuario )

order by
	planeacion.desde;