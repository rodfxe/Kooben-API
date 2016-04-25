select
	dia.id,
	dia.planeacionId,
	dia.fecha,
	dia.personas

from cmt_planeaciondias as dia

inner join cmt_planeacion as planeacion
	on ( planeacion.id = dia.planeacionId and planeacion.activo = 1 )

where
	( :id = -1 or dia.id = :id ) and
	( :planeacion = -1 or dia.planeacionId = :planeacion ) and
	( :fecha = -1 or dia.fecha = :fecha );