SELECT 
  type.IdTipoInsumo as id,
  type.CodigoTipoInsumo as code,
  type.NombreTipoInsumo as name,
  type.DescripcionTipoInsumo as description

FROM
  cmt_tipoinsumo as type

WHERE
  ( :IdTipoInsumo = -1 or type.IdTipoInsumo = :IdTipoInsumo ) and
  ( :CodigoTipoInsumo = -1 or type.CodigoTipoInsumo LIKE :CodigoTipoInsumo ) and 
  ( :NombreTipoInsumo = -1 or type.NombreTipoInsumo = :NombreTipoInsumo ) and 
  ( :Activo = -1 or ( :Activo <> -1 and type.Activo = :Activo ) )

ORDER BY
  IF( :Orden = -1, IF( :Orden = "Codigo", type.CodigoTipoInsumo, type.NombreTipoInsumo ), type.NombreTipoInsumo )