<?php
/**
* Supplies types routes for Kooben
*
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/



/**
 * Obtener la lista de los tipos de productos
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/supplies-types', function() use($mysql, $kooben){
	$types = new Model( 'suppliesTypes', $mysql );
	$types->setProperties( $kooben->models->suppliesTypes );

	echo $types->findAll()->toJson();
});

?>
