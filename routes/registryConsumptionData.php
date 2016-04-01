<?php
/**
* Registry consumption data routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/


$app->get( '/registryConsumptionData/filter/menu/:menuId', function($menuId) use( $app, &$mysql, $kooben ){
	$menuId = intval($menuId);
	$recipes = new Model( 'registryConsumptionData', $mysql );
	$recipes->setProperties( $kooben->models->registryConsumptionData );

	$result = $recipes->find( new QueryParams( array(
		'IdMenu' => new QueryParamItem( $menuId )
	) ) );
	$result->menuId = $menuId;
	echo $result->toJson();
});







$app->post( '/registryConsumptionData/period/:periodId/consumption/:consumptionId', function($periodId, $consumptionId) use( $app, &$mysql, $kooben ){
	$periodId = intval($periodId);
	$consumptionId = intval($consumptionId);

	$session = checkKoobenSession($app);
	if ( !$session->status->found ){ echo createEmptyModelWithStatus( 'Post', KOOBEN_SESSION_INVALID )->toJson(); return; }

	# initialize the data.
	$data = new stdClass();
	$data->items = $app->request->post( 'items' );

	$recipes = new Model( 'registryConsumptionData', $mysql );
	$recipes->setProperties( $kooben->models->registryConsumptionData );

	$deleteResult = $recipes->customDelete( array(
		'params' => new QueryParams( array(
			'periodId' => new QueryParamItem( $periodId ),
			'consumptionId' => new QueryParamItem( $consumptionId )
		) )
	) );
	
	$result = $recipes->multiCreate( array(
		'items' => $data->items,
		'rules' => array( PARAM_INT, PARAM_INT, PARAM_INT ),
		'getQueryParams' => new QueryParams( array(
			'IdPeriodoConsumo' => new QueryParamItem( $periodId ),
			'IdRegistroConsumo' => new QueryParamItem( $consumptionId )
		) )
	) );

	$result->deleted = $deleteResult;
	echo $result->toJson();
});


?>