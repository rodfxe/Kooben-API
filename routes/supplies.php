<?php
/**
* Supplie routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get al supplies
$app->get( '/supplies', function() use( $mysql, $kooben ){
	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );

	echo $supplies->findAll()->toJson();
});


# get al supplies for upload image
$app->get( '/supplies-for-upload', function() use( $mysql, $kooben ){
	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );
	$supplies->useQuery( 'get-for-upload' );

	echo $supplies->findAll()->toJson();
});

# get supplies minified
$app->get( '/supplies/light', function() use( $mysql, $kooben ){
	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );
	$supplies->useQuery( 'get-light' );

	echo $supplies->findAll()->toJson();
});












# create a new supplie
$app->post( '/supplies', function() use( $app, $mysql, $kooben, $sessions, $kardex ){
	$session = checkKoobenSession( $app );
	if( !$session->status->found ){
		$result = new StdModel();
        $result->status = new PostModelStatus();

        echo $result->toJson();
		return;
	}

	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );

	$values = $app->request->post();
	$supplies->IdTipoInsumo = $values[ 'type' ];
	$supplies->CodigoInsumo = $values[ 'code' ];
	$supplies->NombreInsumo = $values[ 'name' ];
	$supplies->DescripcionInsumo = $values[ 'description' ];
	$supplies->iIdUnidad = $values[ 'measure' ];
	$supplies->Calorias = $values[ 'calories' ];
	$supplies->Proteinas = $values[ 'proteins' ];
	$supplies->Grasas = $values[ 'greases' ];
	$supplies->HidratosCarbono = $values[ 'carbohydrates' ];
	$supplies->IndiceGlucemico = $values[ 'glycemicIndex' ];
	$supply = $supplies->create();

	if( $supply->status->created ){
		saveInKoobenKardex( $supply->id, $session->id, 'cmt_insumo' );
	}

	echo $supply->toJson();

});









# delete an supply
$app->delete( 'supplies/:id', function($id) use($app, $mysql, $kooben){
	$id = intval($id);
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ){
		$status = new DeleteModelStatus();
		echo $status->toJson();
	}

	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );
	$supply = $supplies->delete( $id );

	if( $supply->status->found ){ saveInKoobenKardex( $id, $session->id, 'cmt_insumo', 'delete' ); }
	echo $supply->toJson();
});



$app->get( '/supplies/:id/marks', function( $id ) use( $mysql, $kooben ) {
	$marks = new Model( 'suppliesMarks', $mysql );
	$marks->setProperties( $kooben->models->suppliesMarks );
	echo $marks->findBy([
		'queryName' => 'get-with-detail',
		'params' => new QueryParams([
			'supplyId' => new QueryParamItem( $id )
		])
	])->toJson();
});


$app->get( '/supplies/:supplyid/presentations', function( $supplyid ) use( $mysql, $kooben ) {
	$presentations = new Model( 'presentations', $mysql );
	$presentations->setProperties( $kooben->models->presentations );
	echo $presentations->findBy([
		'queryName' => 'get-for-shop',
		'params' => new QueryParams([
			'supplyId' => new QueryParamItem( $supplyid )
		])
	])->toJson();
});


?>
