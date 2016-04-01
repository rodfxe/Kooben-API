<?php
/**
* Supplies types routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get all types
$app->get( '/supplies-types', function() use($mysql, $kooben){
	$types = new Model( 'suppliesTypes', $mysql );
	$types->setProperties( $kooben->models->suppliesTypes );

	echo $types->findAll()->toJson();
});

?>
