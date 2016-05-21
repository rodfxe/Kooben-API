/* cmt_recetapartida tipo Catalogo */
SELECT DISTINCT
  rpd.IdRecetaPartida as id,
  rpd.IdInsumo supplyId,
  rpd.IdReceta recipeId,
  rpd.Cantidad as cant,
  rpd.iIdUnidad,
  ins.CodigoInsumo as code,
  ins.NombreInsumo as name,
  ins.DescripcionInsumo supplyDescription,
  ins.IndiceGlucemico, 
  ins.iIdUnidad as iIdUnidad_Insumo,
  ins.IdTipoInsumo,
  u.sCodigo um,
  u.sNombre,
  u_ins.sCodigo as umIngredient,
  u_ins.sNombre as sNombre_Insumo,
  tins.CodigoTipoInsumo sNombre_Insumo,
  tins.NombreTipoInsumo as type,
  @PrecioInsumo:=( SELECT
                    pr.PrecioCompra
                  FROM
                    cmt_insumoprecio pr
                  WHERE
                    pr.IdInsumo = rpd.IdInsumo
                  ORDER BY
                    pr.Aplicacion DESC
                  LIMIT 1 ) AS ingredientPrice,

  @Cantidad_Insumo:=ROUND( IF( ins.iIdUnidad = u.iIdUnidad, rpd.Cantidad, 
                          IF( IsNull( con2.IdConversion ), 
                         ( IF( con1.Operador = "Multiplicar", ( con1.Valor * rpd.Cantidad ), ( rpd.Cantidad / con1.Valor ) ) ),
                         ( IF( con2.Operador = "Multiplicar", ( con2.Valor * rpd.Cantidad ), ( rpd.Cantidad / con2.Valor ) ) ) ) ), 2 )  as cantByIngredient,
  
  ROUND( @PrecioInsumo * @Cantidad_Insumo, 2 ) as amount
FROM
  cmt_recetapartida rpd

INNER JOIN 
  cmt_insumo ins
    on ( ins.IdInsumo = rpd.IdInsumo )

INNER JOIN 
  cmt_tipoinsumo tins
    on ( tins.IdTipoInsumo = ins.IdTipoInsumo )

INNER JOIN
  nuc_unidades u
    on ( u.iIdUnidad = rpd.iIdUnidad )

INNER JOIN
  nuc_unidades u_ins
    on ( u_ins.iIdUnidad = ins.iIdUnidad )

LEFT JOIN
  cmt_conversion con1
    on ( con1.iIdUnidad_s = rpd.iIdUnidad and 
        con1.iIdUnidad_t = ins.iIdUnidad )

LEFT JOIN
  cmt_conversion con2
    on ( con2.iIdUnidad_s = rpd.iIdUnidad and 
        con2.iIdUnidad_t = ins.iIdUnidad and 
        con2.IdInsumo = ins.IdInsumo )

WHERE
  ( :IdRecetaPartida = -1 or rpd.IdRecetaPartida = :IdRecetaPartida ) and 
  ( :IdReceta = -1 or rpd.IdReceta = :IdReceta ) and 
  ( :IdInsumo = -1 or rpd.IdInsumo = :IdInsumo ) and
  ( :IdTipoInsumo = -1 or ins.IdTipoInsumo = :IdInsumo ) and
  ( :CodigoInsumo = -1 or ins.CodigoInsumo LIKE :CodigoInsumo ) and
  ( :CodigoTipoInsumo = -1 or tins.CodigoTipoInsumo LIKE :CodigoTipoInsumo )

Group by 
  rpd.IdRecetaPartida

ORDER BY
  IF( :OrdenCodigo = -1, ins.CodigoInsumo, IF( :OrdenCodigo = "Si", ins.CodigoInsumo, ins.NombreInsumo ) )