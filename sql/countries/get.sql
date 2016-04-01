SELECT
  p.IdPais as id,
  p.CodigoPais as code,
  p.TituloPais as name,
  p.Nacionalidad as nationality,
  p.Comentario as comment,
  ( SELECT
     COUNT( c.IdCiudad )
   FROM
     nuc_estado e
   INNER JOIN
     nuc_ciudad c
       ON ( c.IdEstado = e.IdEstado )
   WHERE
     e.IdPais = p.IdPais ) as CuentaHijos
  
FROM
  nuc_pais p

WHERE
  ( :idpais = -1 or ( :idpais <> -1 and p.idpais = :idpais ) ) and
  ( :codigopais = -1 or ( :codigopais <> -1 and p.codigopais LIKE :codigopais ) ) and
  ( :titulopais = -1 or ( :titulopais <> -1 and p.titulopais LIKE :titulopais ) ) and
  ( :nacionalidad = -1 or ( :nacionalidad <> -1 and p.nacionalidad LIKE :nacionalidad ) ) and
  ( :comentario = -1 or ( :comentario <> -1 and p.comentario LIKE :comentario ) ) and 
  ( ( :activo = -1 and p.activo = 'Si' ) or ( :activo <> -1 and p.activo = :activo ) )
  
HAVING
  :Hijos = -1 OR CuentaHijos > 0

ORDER BY
  p.TituloPais