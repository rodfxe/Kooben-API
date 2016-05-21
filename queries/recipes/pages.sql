select
	receta.IdReceta as id,
	receta.NombreReceta as nombre,
	receta.DescripcionReceta as descripcion,
	receta.PreparacionReceta as preparacion

from cmt_receta as receta

where
	( :desde = -1 or receta.IdReceta > :desde ) and
	( receta.Activo = 'Si' )

order by
	receta.IdReceta

limit :cantidad;