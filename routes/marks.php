<?php
/**
* Rutas para Marcas
*
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/

# get all marks
$app->get( '/marks', function() use($mysql, $kooben){
    $marks = new Model( 'marks', $mysql );
    $marks->setProperties( $kooben->models->marks );

    echo $marks->findAll()->toJson();
});



# get only one mark
$app->get( '/marks/:id', function($id) use($mysql, $kooben){
	$id = intval($id);
	$marks = new Model( 'marks', $mysql );
    $marks->setProperties( $kooben->models->marks );

    echo $marks->findById( '__default__', $id )->toJson();
});







$app->post( '/marks', function() use($app, $mysql, $kooben){
	
	$session = checkKoobenSession($app);
    if ( !$session->status->found ){
    	echo createEmptyModelWithStatus( 'Post', KOOBEN_SESSION_INVALID )->toJson();
    	return;
    }

    $values = $app->request->post();
	$marks = new Model( 'marks', $mysql );
    $marks->setProperties( $kooben->models->marks );
    $mark = $marks->setValuesFromArray( $app->request->post() )->create();

    if ( $mark->status->created ){ saveInKoobenKardex( $mark->id, $session->id, 'cmt_marca' ); }
    echo $mark->toJson();
});








$app->put( '/marks/:id', function($id) use($app, $mysql, $kooben){
	$id = intval($id);
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Put' )->toJson();
		return;
	}

	$result = new StdModel();
	$marks = new Model( 'marks', $mysql );
    $marks->setProperties( $kooben->models->marks );
    $marks->IdMarca = $id;
    $result->status = $marks->setValuesFromArray( $app->request->put() )->save();

    if ( $result->status->updated ){ saveInKoobenKardex( $id, $session->id, 'cmt_marca', 'update' ); }
    echo $result->toJson();
});













$app->delete( '/marks/:id', function($id) use($app, $mysql, $kooben){
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		echo createEmptyModelWithStatus( 'Delete' )->toJson();
		return;
	}

	$result = new StdModel();
	$marks = new Model( 'marks', $mysql );
    $marks->setProperties( $kooben->models->marks );
    $result->status = $marks->delete( $id );

    if ( $result->status->deleted ){ saveInKoobenKardex( $id, $session->id, 'cmt_marca', 'delete' ); }
    echo $result->toJson();
});

?>
