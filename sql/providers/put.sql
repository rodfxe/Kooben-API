update nuc_empresas
set
	sNombreCorto = if( :sNombreCorto = '-1',
		sNombreCorto,
		:sNombreCorto
	),

	eRegimenFiscal = if( :eRegimenFiscal = '',
		eRegimenFiscal,
		:eRegimenFiscal
	),

	sRazonSocial = if( :sRazonSocial = '-1',
		sRazonSocial,
		:sRazonSocial
	),

	sRFC = if( :sRFC = '-1',
		sRFC,
		:sRFC
	),

	eTipoEmpresa = if( :eTipoEmpresa = '-1',
		eTipoEmpresa,
		:eTipoEmpresa
	),

	iIdPais = if( :iIdPais = -1,
		iIdPais,
		:iIdPais
	),

	iIdEstado = if( :iIdEstado = -1,
		iIdEstado,
		:iIdEstado
	),

	iIdMunicipio = if( :iIdMunicipio = -1,
		iIdMunicipio,
		:iIdMunicipio
	),

	sCiudad = if( :sCiudad = '-1',
		sCiudad,
		:sCiudad
	),

	sDomicilio = if( :sDomicilio = '-1',
		sDomicilio,
		:sDomicilio
	),

	sCp = if( :sCp = '-1',
		sCp,
		:sCp
	),

	mComentarios = if( :mComentarios = '-1',
		mComentarios,
		:mComentarios
	)

where
	iIdEmpresa = :iIdEmpresa and
	Activo = 'Si';