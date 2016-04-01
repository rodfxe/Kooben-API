select
	distinct
	unix_timestamp( price.Aplicacion ) as id,
	price.Aplicacion as application

from cmt_insumoprecio as price





inner join cmt_insumo as supply
	on( supply.IdInsumo = price.IdInsumo )


inner join nuc_empresas as provider
	on( provider.iIdEmpresa = price.iIdEmpresa )


inner join nuc_pais as country
	on( country.idpais = provider.iIdPais )


inner join nuc_estado as state
	on( state.idestado = provider.iIdEstado )


inner join nuc_ciudad as city
	on( city.idciudad = provider.iIdMunicipio )


inner join cmt_marca as measure
	on(  measure.IdMarca = price.IdMarca )


inner join cmt_presentacion as presentation
	on( presentation.IdPresentacion = price.IdPresentacion )





where
	( price.Aplicacion between :start and :end ) and
	( country.idpais = :country ) and
	( state.idestado = :state ) and
	( city.idciudad = :city ) and
	( price.Activo = 'Si' )





order by
	id;