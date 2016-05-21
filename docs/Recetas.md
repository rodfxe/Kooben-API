Ruteo para recetas
==================


`/recipes` -> Retorna la lista completa de las recetas.


`/recipes/:id` -> Retorna una receta.
`@param` $id int Id de receta


`/recipes/:id/ingredients` -> Retorna la lista de ingredientes de una receta.
`@param` $id int Id de receta


`/recipes/search/:keywords` -> Retorna una lista de recetas filtrada.
`@param` $keywords Palabras a buscar.


`/recetas/pagina/:page/:count` -> Retorna una lista de recetas a partir de un id, y toma como limite una cantidad.
`@param` $from int A partir de que Id de receta se desea empezar a listar.
`@param` $cantidad int Cantidad limite de filas a partir del $from.