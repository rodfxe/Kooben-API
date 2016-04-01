select
	# key fields
	menu.IdMenu as menuId,
	registryConsumption.IdRegistroConsumo as dayId,
	periodConsumption.IdPeriodoConsumo as periodId,
	ingredient.IdReceta as recipeId,
	ingredient.IdInsumo as supplyId,
	recipe.IdReceta as dayMenuId,


	# user data fields
	supply.NombreInsumo as supplyName,
	measure.sNombre as measureName,
	sum( ingredient.Cantidad ) as cant,

	# supply information
	sum( ifnull( (
		select min( p.PrecioVenta )
		from cmt_insumoprecio as p
		where
			p.IdInsumo = supply.IdInsumo and
			p.Aplicacion <= registryConsumption.Fecha
		order by
			p.Aplicacion desc
		limit 1 ), 0.0 ) ) as price



from cmt_recetapartida as ingredient




inner join cmt_insumo as supply
	on( supply.IdInsumo = ingredient.IdInsumo )


inner join nuc_unidades as measure
	on( measure.iIdUnidad = ingredient.iIdUnidad )


inner join cmt_receta as mainRecipe
	on( mainRecipe.IdReceta = ingredient.IdReceta )


inner join cmt_registroconsumodatos as recipe
	on( recipe.IdReceta = ingredient.IdReceta )


inner join cmt_periodoconsumo as periodConsumption
	on( periodConsumption.IdPeriodoConsumo = recipe.IdPeriodoConsumo )


inner join cmt_registroconsumo as registryConsumption
	on( registryConsumption.IdRegistroConsumo = recipe.IdRegistroConsumo )


inner join cmt_menu as menu
	on( registryConsumption.IdMenu = menu.IdMenu )




where
	( :menuId = -1 or menu.IdMenu = :menuId ) and
	( :dayId = -1 or registryConsumption.IdRegistroConsumo = :dayId ) and
	( :periodId = -1 or periodConsumption.IdPeriodoConsumo = :periodId ) and
	( :recipeId = -1 or mainRecipe.IdReceta = :recipeId )

group by
	menu.IdMenu,
	registryConsumption.IdRegistroConsumo,
	supply.IdInsumo


order by
	menu.IdMenu,
	registryConsumption.IdRegistroConsumo,
	periodConsumption.IdPeriodoConsumo