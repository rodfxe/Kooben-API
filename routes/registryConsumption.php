<?php
/**
* Registry consumption routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/registry-consumption/:dayId', function( $dayId ) use ( $mysql, $kooben ){
	$dayId = intval( $dayId );
	$days = new Model( 'registryConsumption', $mysql );
	$days->setProperties( $kooben->models->registryConsumption );
	echo $days->findById( '__default__', $dayId )->toJson();
});




$app->put( '/registry-consumption/:dayId', function( $dayId ) use( $app, $mysql, $kooben ){
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Get' )->toJson();
		return;
	}

	$dayId = intval( $dayId );
	$values = $app->request->put();
	$result = new StdModel();

	$days = new Model( 'registryConsumption', $mysql );
	$days->setProperties( $kooben->models->registryConsumption );
	$day = $days->findById( '__default__', $dayId );

	if ( !$days->status->found ){
		echo createEmptyModelWithStatus( 'Get' )->toJson();
		return;
	}

	$day->IdRegistroConsumo = $dayId;
	$day->Personas = $values[ 'Personas' ];
	$result->status = $day->save();

	if ( $result->status->updated ){
		saveInKoobenKardex( $dayId, $session->id, $days->tableName(), 'update' );
	}

	echo $result->toJson();
});

?>