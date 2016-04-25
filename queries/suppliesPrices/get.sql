/* cmt_insumoprecio tipo Catalog */
SELECT
  ip.IdInsumoPrecio as id,
  ip.IdInsumo as supplyId,
  ins.CodigoInsumo as supplyCode,
  ins.NombreInsumo as supplyName,
  ip.iIdEmpresa as providerId,
  emp.sNombreCorto as providerName,
  emp.sRazonSocial,
  ip.IdMarca as measureId,
  mar.CodigoMarca as measureCode,
  mar.TituloMarca measureName,
  ip.IdPresentacion as presentationId,
  pres.CodigoPresentacion as presentationCode,
  pres.TituloPresentacion as presentationName,
  ip.PrecioCompra as buyPrice,
  ip.PrecioVenta as salePrice,
  ip.Aplicacion as application

FROM
  cmt_insumoprecio ip 
  
INNER JOIN 
  cmt_insumo ins 
    ON ( ins.IdInsumo = ip.IdInsumo )
INNER JOIN
  nuc_empresas emp
    ON ( emp.iIdEmpresa = ip.iIdEmpresa )
INNER JOIN
  cmt_marca mar
    ON ( mar.IdMarca = mar.IdMarca )
INNER JOIN 
  cmt_presentacion pres
    ON ( pres.IdPresentacion = pres.IdPresentacion )
WHERE 
  ( ( :Activo = -1 AND ip.Activo = "Si" ) OR ip.Activo = :Activo ) AND 
  ( :IdInsumo = -1 OR ip.IdInsumo = :IdInsumo ) AND
  ( :IdMarca = -1 OR ip.IdMarca = :IdMarca ) AND 
  ( :IdPresentacion = -1 OR ip.IdPresentacion = :IdPresentacion ) AND
  ( ip.Aplicacion = ( SELECT
                      MAX( ip2.Aplicacion )
                    FROM
                      cmt_insumoprecio ip2
                    WHERE 
                      ip2.IdInsumo = ip.IdInsumo AND 
                      ip2.IdMarca = ip.IdMarca AND
                      ip2.IdPresentacion = ip.IdPresentacion AND
                      ip2.Activo = IF( :Activo = -1, "Si", :Activo ) AND 
                      ip2.Aplicacion <= IF( :Aplicacion = -1, NOW(  ), :Aplicacion ) ) )

GROUP BY
  ip.IdInsumo,
  ip.IdMarca,
  ip.IdPresentacion

ORDER BY
  ip.Aplicacion DESC