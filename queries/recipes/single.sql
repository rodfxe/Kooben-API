/* cmt_receta tipo catalogo */
SELECT
  rec.IdReceta as id,
  rec.NombreReceta as name,
  rec.DescripcionReceta as description,
  rec.PreparacionReceta as method,
  rec.Personas as persons,
  tpr.NombreTipoReceta as type,
  tpr.CodigoTipoReceta as code

FROM
  cmt_receta rec
  
LEFT JOIN 
  cmt_tiporeceta tpr
    on (tpr.IdTipoReceta = rec.IdTipoReceta)

WHERE
  (( :Activo = -1 and rec.Activo = "Si") or rec.Activo = :Activo) and 
  ( :IdReceta = -1 or rec.IdReceta = :IdReceta) and 
  ( :IdTipoReceta = -1 or rec.IdTipoReceta = :IdTipoReceta) and
  ( :Tipo = -1 or rec.Tipo = :Tipo or (IsNull(rec.Tipo) and :Tipo = "Receta")) and 
  ( :CodigoReceta = -1 or rec.CodigoReceta LIKE :CodigoReceta) and
  ( :NombreReceta = -1 or rec.NombreReceta LIKE :NombreReceta) and 
  ( :DescripcionReceta = -1 or rec.DescripcionReceta LIKE :DescripcionReceta) and 
  ( :CodigoTipoReceta = -1 or tpr.CodigoTipoReceta LIKE :CodigoTipoReceta) and 
  ( :NombreTipoReceta = -1 or tpr.NombreTipoReceta LIKE :NombreTipoReceta)

ORDER BY
  tpr.Orden,
  IF( :OrdenCodigo = -1, rec.CodigoReceta, IF( :OrdenCodigo = "Si", rec.CodigoReceta, rec.NombreReceta))