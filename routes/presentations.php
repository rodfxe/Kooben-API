<?php
/**
* Rutas para presentaciones
*
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/


$app->get( '/presentations', function() use($mysql, $kooben){
    $presentations = new Model( 'presentations', $mysql );
    $presentations->setProperties( $kooben->models->presentations );

    echo $presentations->findAll()->toJson();
});





$app->get( '/presentations/:id', function($id) use($mysql, $kooben){
	$id = intval($id);
    $presentations = new Model( 'presentations', $mysql );
    $presentations->setProperties( $kooben->models->presentations );

    echo $presentations->findById( '__default__', $id) ->toJson();
});






$app->post( '/presentations', function() use( $app, $mysql, $kooben ){
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Post' )->toJson();
		return;
	}

	$presentations = new Model( 'presentations', $mysql );
    $presentations->setProperties( $kooben->models->presentations );
    $presentation = $presentations->setValuesFromArray( $app->request->post() )->create();

    if ( $presentation->status->created ){ saveInKoobenKardex( $presentation->id, $session->id, 'cmt_presentacion' ); }
    echo $presentation->toJson();
});








$app->put( '/presentations/:id', function($id) use( $app, $mysql, $kooben ){
	$id = intval($id);
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Put' )->toJson();
		return;
	}

	$presentation = new StdModel();
	$presentations = new Model( 'presentations', $mysql );
	$presentations->setProperties( $kooben->models->presentations );
	$presentations->IdPresentacion = $id;
    $presentation->status = $presentations->setValuesFromArray( $app->request->put() )->save();

    if ( $presentation->status->updated ){ saveInKoobenKardex( $id, $session->id, 'cmt_presentacion', 'update' ); }
    echo $presentation->toJson();
});







$app->delete( '/presentations/:id', function($id) use($app, $mysql, $kooben){
	$id = intval($id);
	$id = intval($id);
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Delete' )->toJson();
		return;
	}
	$presentations = new Model( 'presentations', $mysql );
	$presentations->setProperties( $kooben->models->presentations );

	$result = new StdModel();
	$result->status = $presentations->delete($id);

	if( $result->status->deleted ){ saveInKoobenKardex( $id, $session->id, 'cmt_presentacion', 'delete' ); }
	echo $result->toJson();
});




$app->post( '/presentations/:supply/:mark/:presentation/image', function( $supply, $mark, $presentation ) use( $app, $mysql, $kooben ) {
    $image = new Upload( $_FILES[ 'file' ], [
        'rules' => [ 'image/jpeg', 'image/jpg' ],
        'maxSize' => $kooben->fileSizes->avatar
    ] );

    $response = new KoobenResponse();
    $response->status = $image->getResume();
    if ( $response->status[ 'isValid' ] ) {
        $filename = "presentation_$supply"."_$mark"."_$presentation";
        $response->status[ 'saved' ] = $image->save( $filename );
    }

    echo $response->toJson();
});



?>
