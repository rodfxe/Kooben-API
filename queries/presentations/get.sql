/* cmt_presentacion tipo Catalogo */
SELECT
  pre.IdPresentacion as id,
  pre.CodigoPresentacion as code,
  pre.TituloPresentacion as title,
  pre.TituloPresentacion as name,
  pre.iIdUnidad as measure,
  pre.Cantidad as cant,
  measure.sNombre as measurename,
  ( SELECT
     pxi.IdPresentacionxInsumo
   FROM
     cmt_presentacionxinsumo pxi
   WHERE
     pxi.IdInsumo = :IdInsumo AND
     pxi.IdPresentacion = pre.IdPresentacion ) as IdPresentacionxInsumo

FROM
  cmt_presentacion pre

inner join nuc_unidades as measure
  on( measure.iIdUnidad = pre.iIdUnidad )

WHERE
  ( ( :Activo = -1 and pre.Activo = "Si" ) or pre.Activo = :Activo ) and
  ( :IdPresentacion = -1 or pre.IdPresentacion = :IdPresentacion ) and
  ( :CodigoPresentacion = -1 or pre.CodigoPresentacion LIKE :CodigoPresentacion ) and
  ( :TituloPresentacion = -1 or pre.TituloPresentacion LIKE :TituloPresentacion )

ORDER BY
  IF( :OrdenCodigo = -1, pre.TituloPresentacion, pre.CodigoPresentacion )
