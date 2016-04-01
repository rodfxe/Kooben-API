<?php
/**
* Period consumption routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/period-consumption', function()  use( $mysql, $kooben ){
	$periods = new Model( 'periodConsumption', $mysql );
	$periods->setProperties( $kooben->models->periodConsumption );

	echo $periods->findAll()->toJson();
});


?>