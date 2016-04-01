<?php
/**
* States routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get all states
$app->get( '/states', function() use($mysql, $kooben){
	$states = new Model( 'states', $mysql );
	$states->setProperties( $kooben->models->states );

	echo $states->findAll()->toJson();
});

$app->get( '/states/filter/:params+', function($params) use($mysql, $kooben){
	$states = new Model( 'states', $mysql );
	$states->setProperties( $kooben->models->states );

	$filter = new QueryParams();
	$filter->getFromArray( $params );
	echo $states->find( $filter )->toJson();
});

?>