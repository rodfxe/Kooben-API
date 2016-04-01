select
    price.IdInsumo as id,
    provider.iIdPais as countryId,
    provider.iIdEstado as stateId,
    provider.iIdMunicipio as locationId,

    supply.NombreInsumo as name,
	provider.sNombreCorto as providerName,
	count( mark.IdMarca ) as markCount,
	count( presentation.IdPresentacion ) as presentationCount

from cmt_insumo as supply


inner join cmt_tipoinsumo as type
	on( type.IdTipoInsumo = supply.IdTipoInsumo )


inner join nuc_unidades as unit
	on(	unit.iIdUnidad = supply.iIdUnidad )


left join cmt_insumoprecio as price
	on( price.IdInsumo = supply.IdInsumo and price.precioVenta > 0 )


left join nuc_empresas as provider
	on( provider.iIdEmpresa = price.iIdEmpresa )


left join cmt_marca as mark
	on( mark.IdMarca = price.IdMarca )


inner join cmt_presentacion as presentation
	on( presentation.IdPresentacion = price.IdPresentacion )


where
	( price.Aplicacion <= now() ) and
	( :filterByProviders = 0 or ( :filterByProviders = 1 and provider.iIdEmpresa in ( :providers ) ) )


group by
	price.IdInsumo


order by
	price.IdInsumo,
	price.IdMarca,
	price.IdPresentacion,
	price.iIdEmpresa,
	price.PrecioVenta