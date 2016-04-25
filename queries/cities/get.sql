/* nuc_ciudad tipo Catalogo */
SELECT
  c.IdCiudad as id,
  c.CodigoCiudad as code,
  c.TituloCiudad as name,
  c.Descripcion as description,
  e.CodigoEstado as statecode,
  e.TituloEstado as statename,
  p.CodigoPais as countrycode,
  p.TituloPais as countryname
  
FROM
  nuc_ciudad c

INNER JOIN 
  nuc_estado e
    ON ( e.IdEstado = c.IdEstado )

INNER JOIN
  nuc_pais p
    ON ( p.IdPais = e.IdPais )

WHERE
  ( :idciudad = -1 or ( :idciudad <> -1 and c.idciudad = :idciudad ) ) and
  ( :idestado = -1 or ( :idestado <> -1 and c.idestado = :idestado ) ) and
  ( :codigociudad = -1 or ( :codigociudad <> -1 and c.codigociudad LIKE :codigociudad ) ) and
  ( :titulociudad = -1 or ( :titulociudad <> -1 and c.titulociudad LIKE :titulociudad ) ) and
  ( :descripcion = -1 or ( :descripcion <> -1 and c.descripcion LIKE :descripcion ) )and
  ( ( :activo = -1 and c.activo = 'Si' ) or ( :activo <> -1 and c.activo = :activo ) ) and
  ( :titulopais = -1 or ( :titulopais <> -1 and p.titulopais LIKE :titulopais ) ) and
  ( :tituloestado = -1 or ( :tituloestado <> -1 and e.tituloestado LIKE :tituloestado ) )

ORDER BY 
  p.titulopais,
  e.tituloestado,
  c.titulociudad