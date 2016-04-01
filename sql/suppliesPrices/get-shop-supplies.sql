select
    distinct
    supply.IdInsumo as id,
    supply.NombreInsumo as name

from cmt_insumo as supply


inner join cmt_tipoinsumo as type
	on( type.IdTipoInsumo = supply.IdTipoInsumo )


inner join cmt_insumoprecio as price
	on( price.IdInsumo = supply.IdInsumo and price.precioVenta > 0 )


inner join nuc_empresas as provider
	on( provider.iIdEmpresa = price.iIdEmpresa )


where
	( price.Aplicacion <= now() ) and
	( :filterByProviders = 0 or ( :filterByProviders = 1 and provider.iIdEmpresa in ( :providers ) ) )


order by
	supply.IdInsumo;