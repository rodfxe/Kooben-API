select
	provider.sNombreCorto as providerName,
	provider.sCiudad as providerCity,
	provider.sDomicilio as providerAddress,
	provider.lat,
	provider.lng,
	price.IdInsumoPrecio as id,
	price.iIdEmpresa as providerId,
	price.PrecioVenta as val
from cmt_insumoprecio as price

inner join nuc_empresas as provider
	on ( provider.iIdEmpresa = price.iIdEmpresa )

where
	( price.IdInsumo = :supply ) and
	( price.IdMarca = :mark ) and
	( price.IdPresentacion = :presentation )

order by
	price.Aplicacion desc,
	price.PrecioVenta

limit 1;