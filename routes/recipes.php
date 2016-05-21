<?php
/**
| Rutas para recetas
|
| @author edmsamuel
*/

/**
 * /recipes
 * Retorna la lista completa de las recetas.
 * 
 * @author edmsamuel
 */
$app->get( '/recipes', function(){
	echo Receta::lista()->toJson();
});



/**
 * /recipes/:id
 * Retorna una receta.
 * 
 * @param $id int Id de receta
 * @author edmsamuel
 */
$app->get( '/recipes/:id', function( $id ) {
	echo Receta::byId( $id )->toJson();
});



/**
 * /recipes/:id/ingredients
 * Retorna la lista de ingredientes de una receta.
 * 
 * @param $id int Id de receta.
 * @author edmsamuel
 */
$app->get( '/recipes/:id/ingredients', function( $id ) {
	echo Receta::ingredientes( $id )->toJson();
});



/**
 * /recipes/search/:keywords
 * Retorna una lista de recetas filtrada.
 * 
 * @param $keywords Palabras a buscar.
 * @author edmsamuel
 */
$app->get( '/recipes/search/:keywords', function( $keywords ) {
	echo Receta::buscar( $keywords )->toJson();
});



/**
 * /recetas/pagina/:page/:count
 * Retorna una lista de recetas a partir de un id, y toma como limite una cantidad.
 * 
 * @param $from int A partir de que Id de receta se desea empezar a listar.
 * @param $cantidad int Cantidad limite de filas a partir del $from.
 * @author edmsamuel
 */
$app->get( '/recetas/paginar/:from/:count', function( $from, $cantidad ) {
	echo Receta::pagina( $from, $cantidad )->toJson();
});
