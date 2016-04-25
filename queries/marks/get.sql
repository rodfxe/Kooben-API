/* cmt_receta tipo Catalogo */
SELECT
  mca.IdMarca as id,
  mca.CodigoMarca as code,
  mca.TituloMarca as name,
  ( SELECT
     mxi.IdMarcaxInsumo
   FROM
     cmt_marcaxinsumo mxi
   WHERE
     mxi.IdInsumo = :IdInsumo AND
     mxi.IdMarca = mca.IdMarca ) as IdMarcaxInsumo

FROM
  cmt_marca mca

WHERE
  ( :IdMarca = -1 or mca.IdMarca = :IdMarca ) and
  ( :CodigoMarca = -1 or mca.CodigoMarca LIKE :CodigoMarca ) and
  ( :TituloMarca = -1 or mca.TituloMarca LIKE :TituloMarca ) and
  ( mca.Activo = 'Si' )

ORDER BY
  IF( :OrdenCodigo = -1, mca.TituloMarca, mca.CodigoMarca )
