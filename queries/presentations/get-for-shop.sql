select
	item.IdInsumoPrecio as id,
	item.IdInsumo as supplyId,
	item.IdMarca as markId,
	item.IdPresentacion as presentationId,

	supply.NombreInsumo as supplyName,
	mark.TituloMarca as markName,
	presentation.TituloPresentacion as name,
	units.sNombre as unitName,
	units.sSigla as unitInitials

from cmt_insumoprecio as item

inner join cmt_insumo as supply
	on ( supply.IdInsumo = item.IdInsumo )

inner join cmt_marca as mark
	on ( mark.IdMarca = item.IdMarca )

inner join cmt_presentacion as presentation
	on ( presentation.IdPresentacion = item.IdPresentacion )

inner join nuc_unidades as units
	on ( units.iIdUnidad = presentation.iIdUnidad )

where
	( :id = -1 or item.IdInsumoPrecio = :id ) and
	( :supplyId = -1 or item.IdInsumo = :supplyId ) and
	( :presentationId = -1 or item.IdPresentacion = :presentationId ) and
	( presentation.Activo = 'Si' )

group by
	item.IdInsumo,
	item.IdMarca,
	item.IdPresentacion