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
  ( :CodigoReceta = -1 or recipe.CodigoReceta LIKE :CodigoReceta ) and
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
  recipe.IdReceta