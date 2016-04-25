select
	provider.sNombreCorto as providerName,
	provider.sCiudad as providerCity,
	provider.sDomicilio as providerAddress,
	provider.lat,
	provider.lng,
	supply.NombreInsumo as supplyName,
	presentation.TituloPresentacion as presentationName,
	mark.TituloMarca as markName,
	price.IdInsumoPrecio as id,
	price.iIdEmpresa as providerId,
	price.PrecioVenta as val

from cmt_insumoprecio as price

inner join nuc_empresas as provider
	on ( provider.iIdEmpresa = price.iIdEmpresa )

inner join cmt_insumo as supply
	on ( supply.IdInsumo = price.IdInsumo )

inner join cmt_presentacion as presentation
	on ( presentation.IdPresentacion = price.IdPresentacion )

inner join cmt_marca as mark
	on ( mark.IdMarca = price.IdMarca )

where
	( price.IdInsumo = :supply ) and
	( price.IdMarca = :mark ) and
	( price.IdPresentacion = :presentation )

order by
	price.Aplicacion desc,
	price.PrecioVenta

limit 1;