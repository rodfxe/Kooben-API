select
	distinct supply.IdInsumo as id,
	supply.NombreInsumo as name

from cmt_insumo as supply

inner join cmt_tipoinsumo as type
	on( type.IdTipoInsumo = supply.IdTipoInsumo )

inner join nuc_unidades as measure
	on(	measure.iIdUnidad = supply.iIdUnidad )

inner join cmt_insumoprecio as price
	on( price.IdInsumo = supply.IdInsumo and
		price.precioVenta > 0 )

where
	( supply.eClasificacion = 'basica' )

order by
	supply.IdInsumo;
