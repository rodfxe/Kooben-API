<?php
/**
* Providers routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/


$app->get( '/providers', function() use($mysql, $kooben){
    $providers = new Model( 'providers', $mysql );
    $providers->setProperties( $kooben->models->providers );
    echo $providers->findAll()->toJson();
});













$app->get( '/providers/:id', function($id) use($mysql, $kooben){
	$id = intval($id);
	$providers = new Model( 'providers', $mysql );
    $providers->setProperties( $kooben->models->providers );

    echo $providers->findById( '__default__', $id )->toJson();
});

















$app->post( '/providers', function() use( $app, $mysql, $kooben ){
    $session = checkKoobenSession($app);
    if ( !$session->status->found ){
    	echo createEmptyModelWithStatus( 'Post' )->toJson();
    	return;
    }

    $providers = new Model( 'providers', $mysql );
    $providers->setProperties( $kooben->models->providers );
	$provider = $providers->setValuesFromArray( $app->request->post() )->create();

	if ( $provider->status->created ){ saveInKoobenKardex( $provider->id, $session->id, 'nuc_empresas' ); }
	echo $provider->toJson();
});














$app->put( '/providers/:id', function($id) use( $app, $mysql, $kooben ){
	$id = intval($id);
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Put' )->toJson();
		return;
	}


	$result = new StdModel();
	$providers = new Model( 'providers', $mysql );
    $providers->setProperties( $kooben->models->providers );
	$providers->iIdEmpresa = $id;
    $result->status = $providers->setValuesFromArray( $app->request->put() )->save();

	if ( $result->status->updated ){ saveInKoobenKardex( $id, $session->id, 'nuc_empresas', 'update' ); }
	echo $result->toJson();
});



















$app->delete( '/providers/:id', function($id) use( $app, $mysql, $kooben ){
	$id = intval($id);
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Delete' );
		return;
	}

	$result = new StdModel();
	$providers = new Model( 'providers', $mysql );
    $providers->setProperties( $kooben->models->providers );
    $result->status = $providers->delete( $id );

    if( $result->status->deleted ){ saveInKoobenKardex( $id, $session->id, 'nuc_empresas', 'delete' ); }
    echo $result->toJson();
});


?>
