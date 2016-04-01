SELECT
    emp.iIdEmpresa as id,
    emp.eRegimenFiscal,
    emp.sNombreCorto as name,
    emp.sRazonSocial,
    emp.eTipoEmpresa as type,
    emp.sRFC as rfc,
    emp.sCiudad as city,
    emp.sDomicilio,
    emp.sCp as cp,
    emp.mComentarios as comments,
    emp.iIdPais as countryId,
    emp.iIdEstado as stateId,
    emp.iIdMunicipio as municipalityId,
    p.titulopais as country,
    e.tituloestado as state,
    c.titulociudad as municipality,
    emp.lat as latitude,
    emp.lng as longitude


FROM
    nuc_empresas emp
        LEFT JOIN nuc_estado e ON ( emp.iIdEstado = e.idestado )
        LEFT JOIN nuc_pais p ON ( emp.iIdPais = p.idpais )
        LEFT JOIN nuc_ciudad c ON ( emp.iIdMunicipio = c.idciudad )

WHERE
    ( :iIdEmpresa = -1 or emp.iIdEmpresa = :iIdEmpresa ) and
    ( :sNombreCorto = -1 or emp.sNombreCorto LIKE :sNombreCorto ) and
    ( emp.Activo = "Si" )

ORDER BY
    emp.iIdEmpresa,
    emp.eRegimenFiscal,
    emp.sNombreCorto;