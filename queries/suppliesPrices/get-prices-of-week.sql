select
	supply.IdInsumo as supply,
	provider.iIdEmpresa as provider,
	price.Aplicacion as application,
	measure.TituloMarca as measure,
	presentation.TituloPresentacion as presentation,
	( select p.PrecioVenta
	  from cmt_insumoprecio as p
	  where
	  	p.iIdEmpresa = provider.iIdEmpresa and
	  	p.IdInsumo = supply.IdInsumo and
	  	p.Aplicacion <= date_add( :week, interval 1 week )
	  order by p.Aplicacion desc
	  limit 1 ) as val

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
	( supply.eClasificacion = 'basica' ) and
	( price.Aplicacion <= date_add( date_add( :week, interval 1 week ), interval -1 day ) ) and
	( provider.iIdPais = :country ) and
	( provider.iIdEstado = :state ) and
	( provider.iIdMunicipio = :city )






group by
	price.IdInsumo,
	price.iIdEmpresa






order by
	supply.IdInsumo,
	provider.iIdEmpresa,
	price.Aplicacion desc;
