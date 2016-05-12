SELECT
  ins.IdInsumo as id,
  ins.IdTipoInsumo,
  tins.CodigoTipoInsumo,
  tins.NombreTipoInsumo,
  ins.CodigoInsumo,
  ins.NombreInsumo as name,
  ins.DescripcionInsumo as description,
  cast( ins.DescripcionInsumo as char(70) ) as lightDescription,
  ins.iIdUnidad as measureId,
  u.sCodigo as code,
  u.sNombre,
  u.sSigla,
  ins.Calorias,
  ins.Proteinas,
  ins.Grasas,
  ins.HidratosCarbono,
  ins.IndiceGlucemico

FROM
  cmt_insumo ins

INNER JOIN
  cmt_tipoinsumo tins
    on ( tins.IdTipoInsumo = ins.IdTipoInsumo )

INNER JOIN
  nuc_unidades u
    on ( u.iIdUnidad = ins.iIdUnidad )

WHERE
  ( :IdInsumo = -1 or ins.IdInsumo = :IdInsumo ) and
  ( :IdTipoInsumo = -1 or ins.IdTipoInsumo = :IdTipoInsumo ) and
  ( :CodigoInsumo = -1 or ins.CodigoInsumo LIKE :CodigoInsumo ) and
  ( :CodigoBuscar = -1 or ( ins.CodigoInsumo = :CodigoBuscar OR ( ins.CodigoInsumo LIKE CONCAT( "%", :CodigoBuscar, "%" ) or ins.NombreInsumo LIKE CONCAT( "%", :CodigoBuscar, "%" ) ) ) ) and
  ( :iIdUnidad = -1 or ins.iIdUnidad = :iIdUnidad ) and
  ( :sCodigo = -1 or u.sCodigo LIKE :sCodigo ) and
  ( :CodigoTipoInsumo = -1 or tins.CodigoTipoInsumo = :CodigoTipoInsumo ) and
  ( ins.Activo = "Si" )

ORDER BY
  ins.NombreInsumo;