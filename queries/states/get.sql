SELECT 
	e.idestado as id,
	e.idpais as country,
	e.codigoestado as code,
	e.tituloestado as name,
	e.descripcion as description,
	p.codigopais as countrycode,
	p.titulopais as countryname

FROM nuc_estado e

INNER JOIN nuc_pais p
	ON ( e.idpais = p.idpais )


WHERE
	( :idestado = -1 or ( :idestado <> -1 and e.idestado = :idestado ) ) and
	( :idpais = -1 or ( :idpais <> -1 and e.idpais = :idpais ) ) and  
	( :codigoestado = -1 or ( :codigoestado <> -1 and e.codigoestado LIKE :codigoestado ) ) and
	( :tituloestado = -1 or ( :tituloestado <> -1 and e.tituloestado LIKE :tituloestado ) ) and
	( :descripcion = -1 or ( :descripcion <> -1 and e.descripcion LIKE :descripcion ) )and
	( ( :activo = -1 and e.activo = 'Si' ) or ( :activo <> -1 and e.activo = :activo ) )  and
	( :titulopais = -1 or ( :titulopais <> -1 and p.titulopais LIKE :titulopais ) ) 


ORDER BY p.titulopais,e.tituloestado