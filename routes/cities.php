<?php
/**
* Cities routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get all cities
$app->get( '/cities', function() use($mysql, $kooben){
	$cities = new Model( 'cities', $mysql );
	$cities->setProperties( $kooben->models->cities );

	echo $cities->findAll()->toJson();
});



$app->get( '/cities/filter/:params+', function($params) use($mysql, $kooben){
	$cities = new Model( 'cities', $mysql );
	$cities->setProperties( $kooben->models->cities );

	$filter = new QueryParams();
	$filter->getFromArray( $params );
	echo $cities->find( $filter )->toJson();
});



?>