select
	data.IdRegistroConsumoDatos as id,
	data.IdPeriodoConsumo as periodConsumption,
	data.IdRegistroConsumo as registryConsumption,
	data.IdReceta as recipeId,
	recipe.NombreReceta as name

from cmt_registroconsumodatos as data

inner join cmt_registroconsumo as registryConsumption
	on( registryConsumption.IdRegistroConsumo = data.IdRegistroConsumo )

inner join cmt_periodoconsumo as periodConsumption
	on( periodConsumption.IdPeriodoConsumo = data.IdPeriodoConsumo )

inner join cmt_menu as menu
	on( menu.IdMenu = registryConsumption.IdMenu )

inner join cmt_receta as recipe
	on( recipe.IdReceta = data.IdReceta )

where
	( :IdPeriodoConsumo = -1 or data.IdPeriodoConsumo = :IdPeriodoConsumo ) and
	( :IdRegistroConsumo = -1 or data.IdRegistroConsumo = :IdRegistroConsumo ) and
	( :IdMenu = -1 or menu.IdMenu = :IdMenu ) and
	( :IdReceta = -1 or data.IdReceta = :IdReceta ) and
	( data.Activo = 'Si' )

order by
	data.IdPeriodoConsumo,
	data.IdRegistroConsumo,
	data.IdReceta;