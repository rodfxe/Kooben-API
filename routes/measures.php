<?php
/**
* Measure routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get all measures
$app->get( '/measures', function() use( $mysql, $kooben ){
	$measures = new Model( 'measures', $mysql );
	$measures->setProperties( $kooben->models->measures );

	echo $measures->findAll()->toJson();
});

?>
