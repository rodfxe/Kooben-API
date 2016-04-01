<?php
/**
* Countries routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get all countries
$app->get( '/countries', function() use($mysql, $kooben){
	$countries = new Model( 'countries', $mysql );
	$countries->setProperties( $kooben->models->countries );

	echo $countries->findAll()->toJson();
});

?>