select
    price.IdInsumo as supplyId,
    price.IdMarca as measureId,
    price.IdPresentacion as presentationId,
    price.iIdEmpresa as providerId,
    price.IdInsumoPrecio as id,
    provider.iIdPais as countryId,
    provider.iIdEstado as stateId,
    provider.iIdMunicipio as locationId,

    supply.NombreInsumo as supplyName,
    measure.TituloMarca as measureName,
	presentation.TituloPresentacion as presentationName,
	provider.sNombreCorto as providerName,

	( select p.Aplicacion
		from cmt_insumoprecio as p
	  where
	  	p.iIdEmpresa = price.iIdEmpresa and
	  	p.IdInsumo = price.IdInsumo and
	  	p.Aplicacion <= now()
	  order by p.Aplicacion desc
	  limit 1 ) as application,

	( select p.PrecioVenta
		from cmt_insumoprecio as p
	  where
	  	p.iIdEmpresa = price.iIdEmpresa and
	  	p.IdInsumo = price.IdInsumo and
	  	p.Aplicacion <= now()
	  order by p.Aplicacion desc
	  limit 1 ) as price,

	unit.sSigla as unit

from cmt_insumo as supply


inner join cmt_tipoinsumo as type
	on( type.IdTipoInsumo = supply.IdTipoInsumo )


inner join nuc_unidades as unit
	on(	unit.iIdUnidad = supply.iIdUnidad )


left join cmt_insumoprecio as price
	on( price.IdInsumo = supply.IdInsumo and price.precioVenta > 0 )


left join nuc_empresas as provider
	on( provider.iIdEmpresa = price.iIdEmpresa )


left join cmt_marca as measure
	on( measure.IdMarca = price.IdMarca )


inner join cmt_presentacion as presentation
	on( presentation.IdPresentacion = price.IdPresentacion )


where
	( price.Aplicacion <= now() ) and
	( :filterByProviders = 0 or ( :filterByProviders = 1 and provider.iIdEmpresa in ( :providers ) ) )


group by
	price.IdInsumo,
	price.IdMarca,
	price.IdPresentacion,
	price.iIdEmpresa


order by
	price.IdInsumo,
	price.IdMarca,
	price.IdPresentacion,
	price.iIdEmpresa,
	price.PrecioVenta