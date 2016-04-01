select
	distinct
	provider.iIdEmpresa as id,
	provider.sNombreCorto as name


from nuc_empresas as provider








left join nuc_estado e
	on ( provider.iIdEstado = e.idestado )


left join nuc_pais p
	on ( provider.iIdPais = p.idpais )


left join nuc_ciudad c
	on ( provider.iIdMunicipio = c.idciudad )


inner join cmt_insumoprecio as price
	on( price.iIdEmpresa = provider.iIdEmpresa )





where
	( price.Aplicacion between :start and :end ) and
	( provider.iIdPais = :country ) and
	( provider.iIdEstado = :state ) and
	( provider.iIdMunicipio = :city ) and
	( provider.Activo = 'Si' )






order by
   provider.iIdEmpresa,
   provider.eRegimenFiscal,
   provider.sNombreCorto;