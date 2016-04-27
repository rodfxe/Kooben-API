select
	item.id,
	item.purchaseId,
	item.productId,
	item.cant,
	price.PrecioVenta as price,
	provider.sNombreCorto as providerName,
	provider.sDomicilio as providerAddress,
	supply.NombreInsumo as supplyName,
	presentation.TituloPresentacion as presentationName

from shop_purchases_items as item

inner join cmt_insumoprecio as price
	on( price.IdInsumoPrecio = item.productId )

inner join nuc_empresas as provider
	on( provider.iIdEmpresa = price.iIdEmpresa )

inner join cmt_presentacion as presentation
	on( presentation.IdPresentacion = price.IdPresentacion )

inner join cmt_insumo as supply
	on( supply.IdInsumo = price.IdInsumo )

where
	( :id = -1 or item.id = :id ) and
	( :purchaseId = -1 or item.purchaseId = :purchaseId ) and
	( :productId = -1 or item.productId = :productId );