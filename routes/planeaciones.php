<?php
/**
* Rutas para la pagina de planeaciones
*
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/


/**
* Obtener las paneaciones del usuario
*
* @return Array lista de planeaciones
* 
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->get( '/planeaciones', function() use ( $app ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) { echo createEmptyModelWithStatus( 'Get' )->toJson(); return; }

	$planeaciones = new Planeacion();
	echo $planeaciones->findBy([
		'params' => new QueryParams([
			'usuario' => new QueryParamItem( $session->userId )
		])
	])->toJson();

});



/**
* Obtener una planeación especifica junto con sus días y recetas.
*
* @param Int $planeacion Id de la planeación a buscar.
* @return KoobenResponse Planeación con días y recetas.
* 
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->get( '/planeaciones/:planeacion', function( $id ) use ( $app ) {
	$planeaciones = new Planeacion();
	$planeacion = $planeaciones->findById( '__default__', $id );
	if ( !$planeacion->status->found ) { echo createEmptyModelWithStatus('Get')->toJson(); return; }

	$planeaciones->dias = Planeacion::obtenerDias( $id );
	$planeacion->recetas = Planeacion::obtenerRecetas( $id );

	echo $planeacion->toJson();
});



/**
* Resumen de una planeación
*
* @param Int $planeacion Id de la planeación a buscar.
* @return KoobenResponse Planeación con días y recetas.
* 
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->get( '/planeaciones/:planeacion/resumen', function( $planeacionId ) {
    echo Planeacion::obtenerResumen( $planeacionId )->toJson();
});



/**
 * Cotizacion de una planeación
 *
 * @param Int $planeacion Id de la planeación a buscar.
 * @return KoobenResponse Planeación con días y recetas.
 * 
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/planeaciones/:planeacion/cotizacion/:tipo/:lat/:lng/:rng', function( $planeacionId, $tipo, $lat, $lng, $rng ) {
	echo Planeacion::cotizacion( $planeacionId, $tipo, $lat, $lng, $rng )->toJson();
});



/**
 * Cotizacion de una planeación por un solo proveedor
 *
 * @param Int $planeacion Id de la planeación a buscar.
 * @param Int $proveedor Id del proveedor
 * @return KoobenResponse Planeación con días y recetas.
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/planeaciones/:planeacion/cotizacion/:tipo/proveedor/:proveedor', function( $planeacionId, $tipo, $proveedorId ) {
	echo Planeacion::cotizacion( $planeacionId, $tipo, -1, -1, -1, $proveedorId )->toJson();
});



/**
* Crear una nueva planeación
*
* @return KoobenResponse Planeacion con sus días.
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->post( '/planeaciones', function() use ( $app ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) { echo createSessionInvalidResponse( 'Post' )->toJson(); return; }

	$post = $app->request->post();
	$planeaciones = new Planeacion();
	$planeaciones->idUsuario = $session->userId;
	$planeaciones->titulo = $post[ 'titulo' ];
	$planeaciones->desde = $post[ 'desde' ];
	$planeaciones->hasta = $post[ 'hasta' ];

	$planeacion = $planeaciones->create();
	if ( !$planeacion->status->created ){ echo $planeacion->toJson(); return; }

	$fechas = [];
	foreach ( $post[ 'dias' ] as $key => $fecha ) {
		array_push( $fechas, [
			$planeacion->id,
			$fecha, 0
		] );
	}

	$dias = new PlaneacionDias();
	$planeacion->dias = $dias->multiCreate([
		'items' => $fechas,
		'rules' => [ PARAM_INT, PARAM_STRING, PARAM_INT ],
		'getQueryParams' => new QueryParams([
			'planeacion' => new QueryParamItem( $planeacion->id )
		]),
		'devMode' => true
	]);

	echo $planeacion->toJson();
});



/**
* Actualizar un día de una planeación.
* Solo se puede actualizar la cantidad de personas del día.
*
* @param Int $planeacion Id de planeación
* @param Int $dia Id del día
* @return KoobenResponse Respuesta de cambio
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->put( '/planeaciones/:planeacion/:dia', function( $planeacion, $diaId ) use( $app ) {
	$dias = new PlaneacionDias();
	$dia = $dias->findById( '__default__', $diaId );

	if ( !$dia->status->found ) { echo createEmptyModelWithStatus('Post')->toJson(); return; }
	$dia->personas = $app->request->post()[ 'personas' ];
	$dia->save();

	echo $dia->toJson();
});



/**
* Asigna una receta para un día de una planeación
*
* @param Int $planeacion Id de planeación
* @param Int $dia Id del día
* @return KoobenResponse Nueva receta asignada
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->post( '/planeaciones/:planeacion/:dia/recetas', function( $planeacion, $dia ) use( $app ) {
	$recetas = new PlaneacionRecetas();
	$recetas->planeacionId = $planeacion;
	$recetas->diaId = $dia;
	$recetas->setValuesFromArray( $app->request->post() );
	$receta = $recetas->create();

	echo $receta->toJson();
});



/**
 * Actualiza los datos de la asignacion de una receta a una planeación.
 *
 * @param $id int Id de asignación
 * @return KoobenResponse Resultado de la operación
 * @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->put( '/planeacion/recetas/:asignacion', function( $id ) use ( $app ) {
    echo Planeacion::actualizarReceta( $id, $app->request->post() )->toJson();
});



/**
* Elimina la asignación de una receta para un día de una planeación
*
* @param Int $receta Id de asignacion
* @return KoobenResponse Respuesta de eliminación
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
$app->delete( '/planeaciones/recetas/:receta', function( $receta ) {
	$recetas = new PlaneacionRecetas();
	echo $recetas->delete( $receta )->toJson();
});

?>