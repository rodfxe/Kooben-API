<?php
/**
* Rutas para productos
*
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/



/**
 * Obtiene la lista completa de todos los suministros
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->get( '/supplies', function() use( $mysql, $kooben ){
	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );

	echo $supplies->findAll()->toJson();
});



/**
 * Obtiene una lista ligera de los productos para la pagina de upload
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/supplies-for-upload', function() use( $mysql, $kooben ){
	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );
	$supplies->useQuery( 'get-for-upload' );

	echo $supplies->findAll()->toJson();
});



/**
 * Obtiene la lista ligera de productos
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/supplies/light', function() use( $mysql, $kooben ){
	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );
	$supplies->useQuery( 'get-light' );

	echo $supplies->findAll()->toJson();
});



/**
 * Crea un nuevo producto
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
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



/**
 * Elimina un producto
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
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



/**
 * Obtiene las marcas asignadas a un prdoucto
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
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



/**
 * Obtiene las presentaciones asignadas a un producto
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
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



/**
 * Carga una imagen de producto
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->post( '/supplies/:id/image', function( $id ) use ( $mysql, $kooben ) {
	$image = new Upload( $_FILES[ 'file' ], [
        'rules' => [ 'image/jpeg', 'image/jpg' ],
        'maxSize' => $kooben->fileSizes->avatar
    ] );

    $response = new KoobenResponse();
    $response->status = $image->getResume();
    if ( $response->status[ 'isValid' ] ) {
        $filename = "supply_$id";
        $response->status[ 'saved' ] = $image->save( $filename );
    }

    echo $response->toJson();
});



/**
 * Obtiene los precios de un producto y marca
 *
 * @param $producto int Id de producto
 * @param $marca int Id de marca
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/supplies/:producto/mark/:marca/prices', function( $producto, $marca = -1 ) {
    echo Producto::precios( $producto, $marca )->toJson();
});



/**
 * Asigna una marca a un producto
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->post( '/supplies-marks', function() use( $app ) {
    $session = checkKoobenSession( $app );
    if ( !$session->status->found ) {
        echo createSessionInvalidResponse( 'Get' )->toJson();
    } else {
        $marca = Producto::asignarMarca( $app->request->post() );

        if ( $marca->status->created ) {
            saveInKoobenKardex( $marca->id, $session->id, $marca->tableName(), 'insert' );
        }

        echo $marca->toJson();
    }
});



/**
 * Crea un nuevo precio
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->post( '/prices', function() use( $app ) {
    $session = checkKoobenSession( $app );
    if ( !$session->status->found ) {
        echo createSessionInvalidResponse( 'Get' )->toJson();
    } else {
        $precio = Producto::crearPrecio( $app->request->post() );

        if ( $precio->status->created ) {
            saveInKoobenKardex( $precio->id, $session->id, $precio->tableName(), 'insert' );
        }

        echo $precio->toJson();
    }
});



/**
 * Elimina marca
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->delete( '/supplies-marks/:asignacion', function( $asignacion ) use( $app ) {
    $session = checkKoobenSession( $app );
    if ( !$session->status->found ) {
        echo createSessionInvalidResponse( 'Delete' )->toJson();
    } else {
        $marca = Producto::eliminarMarca( $asignacion );

        if ( $marca->status->deleted ) {
            saveInKoobenKardex( $asignacion, $session->id, 'cmt_marcaxinsumo', 'delete' );
        }

        echo $marca->toJson();
    }
});



/**
 * Elimina precio
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->delete( '/prices/:precio', function( $precioId ) use( $app, $kooben ) {
    $session = checkKoobenSession( $app );

    if ( !$session->status->found ) {
        echo createSessionInvalidResponse( 'Delete' )->toJson();
    } else {
        $precio = Producto::eliminarPrecio( $precioId );

        if ( $precio->status->deleted ) {
            saveInKoobenKardex( $precioId, $session->id, $kooben->getTableNameOf( 'suppliesPrices' ), 'delete' );
        }

        echo $precio->toJson();
    }
});
?>