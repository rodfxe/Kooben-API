select
	mark.IdMarca as markId,
    mark.TituloMarca as name

from cmt_insumoprecio as price

left join nuc_empresas as provider
	on( provider.iIdEmpresa = price.iIdEmpresa )

left join cmt_marca as mark
	on( mark.IdMarca = price.IdMarca )

where
	( :supply = -1 or price.IdInsumo = :supply ) and
	( :filterByProviders = -1 or provider.iIdEmpresa in ( :providers ) )

group by
	price.IdInsumo,
	price.IdMarca

order by
	price.IdInsumo,
	price.IdMarca