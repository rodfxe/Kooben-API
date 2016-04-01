<?php
/**
* Address routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/me/addresses', function() use( $app, $mysql, $kooben ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) {
		echo createEmptyModelWithStatus( 'Get' )->toJson();
		return;
	}


	$addresses = new Model( 'addresses', $mysql );
	$addresses->setProperties( $kooben->models->addresses );
	echo $addresses->findBy([
		'params' => new QueryParams([
			'userId' => new QueryParamItem( $session->userId )
		])
	])->toJson();
});




$app->get( '/me/addresses/:id', function( $id ) use( $mysql, $kooben ) {
	$addresses = new Model( 'addresses', $mysql );
	$addresses->setProperties( $kooben->models->addresses );
	echo $addresses->findById( '__default__', $id )->toJson();
});




$app->post( '/me/addresses', function() use( $app, $mysql, $kooben ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) {
		echo createEmptyModelWithStatus( 'Post' )->toJson();
		return;
	}

	$addresses = new Model( 'addresses', $mysql );
	$addresses->setProperties( $kooben->models->addresses );
	$addresses->setValuesFromArray( $app->request->post() );
	$addresses->userId = $session->userId;
	$address = $addresses->create();

	if ( $address->status->created ) {
		saveInKoobenKardex( $address->id, $session->id, $addresses->tableName() );
	}

	echo $address->toJson();
});




$app->put( '/me/addresses/:id', function( $id ) use( $app, $mysql, $kooben ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) {
		echo createEmptyModelWithStatus( 'Put' )->toJson();
		return;
	}

	$addresses = new Model( 'addresses', $mysql );
	$addresses->setProperties( $kooben->models->addresses );
	$addresses->setValuesFromArray( $app->request->put() );
	$addresses->id = $id;
	$response = new StdModel();
	$response->status = $addresses->save();

	if ( $response->status->updated ) {
		saveInKoobenKardex( $id, $session->id, $addresses->tableName(), 'update' );
	}

	echo $response->toJson();
});

?>