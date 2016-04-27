/* cmt_receta tipo catalogo */
SELECT
  recipe.IdReceta as id,
  recipe.NombreReceta as name,
  recipe.Personas as persons,
  tpr.NombreTipoReceta as type,
  @ingredientCount := (
    select count( ingredients.IdReceta )
    from cmt_recetapartida as ingredients
    where
      ingredients.IdReceta = recipe.IdReceta
   ) as ingredientCount

FROM
  cmt_receta recipe
  
LEFT JOIN 
  cmt_tiporeceta tpr
    on (tpr.IdTipoReceta = recipe.IdTipoReceta )

WHERE
  ( ( :Cual = -1 and tpr.Cual = "RECETA" ) or tpr.Cual = :Cual ) and 
  ( :IdReceta = -1 or recipe.IdReceta = :IdReceta ) and 
  ( :IdTipoReceta = -1 or recipe.IdTipoReceta = :IdTipoReceta ) and
  ( :Tipo = -1 or recipe.Tipo = :Tipo or (IsNull(recipe.Tipo ) and :Tipo = "Receta" ) ) and 
  ( :CodigoReceta = -1 or recipe.CodigoReceta LIKE :CodigoReceta ) and
  ( :NombreReceta = -1 or recipe.NombreReceta LIKE :NombreReceta ) and 
  ( :DescripcionReceta = -1 or recipe.DescripcionReceta LIKE :DescripcionReceta ) and 
  ( :CodigoTipoReceta = -1 or tpr.CodigoTipoReceta LIKE :CodigoTipoReceta ) and 
  ( :NombreTipoReceta = -1 or tpr.NombreTipoReceta LIKE :NombreTipoReceta ) and
  ( (
    select count( ingredients.IdReceta )
    from cmt_recetapartida as ingredients
    where
      ingredients.IdReceta = recipe.IdReceta
  ) > 0 ) and
  ( recipe.Activo = "Si" )

having
  @ingredientCount > 0

ORDER BY
  tpr.Orden,
  IF( :OrdenCodigo = -1, recipe.CodigoReceta, IF( :OrdenCodigo = "Si", recipe.CodigoReceta, recipe.NombreReceta ) )