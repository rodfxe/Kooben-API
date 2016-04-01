<?php
/**
* Consults for prices routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/prices/basic-basket/compare/:country/:state/:city/:start/:end', function( $country, $state, $city, $start, $end ) use($mysql, $kooben){
	# create result
	$result = new StdModel();

	# model for supplies
	$supplies = new Model( 'supplies', $mysql );
	$supplies->setProperties( $kooben->models->supplies );
	$supplies->useQuery( 'basic-basket' );
	$result->supplies = $supplies->findAll();
	
	# model for days
	$days = new Model( 'suppliesPrices', $mysql );
	$days->setProperties( $kooben->models->suppliesPrices );
	$days->useQuery( 'get-days-basic-basket' );
	# filter for days
	$filterdays = new QueryParams();
	$filterdays->setParam( 'start', $start, PARAM_STRING );
	$filterdays->setParam( 'end', $end, PARAM_STRING );
	$filterdays->setParam( 'country', $country );
	$filterdays->setParam( 'state', $state );
	$filterdays->setParam( 'city', $city );
	$result->days = $days->find($filterdays);





	# model for providers
	$providers = new Model( 'suppliesPrices', $mysql );
	$providers->setProperties( $kooben->models->suppliesPrices );
	# find the providers using params
	$result->providers = $providers->findBy( [
		'params' => new QueryParams( [
			'start' => new QueryParamItem( $start, PARAM_STRING ),
			'end' => new QueryParamItem( $end, PARAM_STRING ),
			'country' => new QueryParamItem( $country ),
			'state' => new QueryParamItem( $state ),
			'city' => new QueryParamItem( $city )
		] ),
		'queryName' => 'get-providers-prices-between-range',
		'devMode' => true
	] );



	# model for prices
	$prices = new Model( 'suppliesPrices', $mysql );
	$prices->setProperties( $kooben->models->suppliesPrices );
	# find the prices using params
	$result->prices = $prices->findBy( [
		'params' => new QueryParams( [
			'start' => new QueryParamItem( $start, PARAM_STRING ),
			'end' => new QueryParamItem( $end, PARAM_STRING ),
			'country' => new QueryParamItem( $country ),
			'state' => new QueryParamItem( $state ),
			'city' => new QueryParamItem( $city )
		] ),
		'queryName' => 'get-prices-between-range',
		'devMode' => true
	] );

	echo $result->toJson();
});


































$app->get( '/prices/best-pantry/compare/weeks/:country/:state/:city/:start/:end', function( $country, $state, $city, $start, $end ) use( $mysql, $kooben ){
	$result = new StdModel();
	$params = new QueryParams();

	$params->setParam( 'country', $country );
	$params->setParam( 'state', $state );
	$params->setParam( 'city', $city );
	$params->setParam( 'start', $start, PARAM_STRING );
	$params->setParam( 'end', $end, PARAM_STRING );


	$Prices = new Model( 'suppliesPrices', $mysql );
	$Prices->setProperties( $kooben->models->suppliesPrices );
	$Prices->useQuery( 'get-prices-of-week' );

	$Providers = new Model( 'suppliesPrices', $mysql );
	$Providers->setProperties( $kooben->models->suppliesPrices );
	$Providers->useQuery( 'get-providers-prices-between-range' );
	$providers = $Providers->find($params);

	$Supplies = new Model( 'supplies', $mysql );
	$Supplies->setProperties( $kooben->models->supplies );
	$Supplies->useQuery( 'basic-basket' );
	$supplies = $Supplies->find($params);

	$prices_week = array();
	$price = array();
	$result->weeks = array();
	$weeks = getWeeksBetween( $start, $end );

	foreach ( $weeks as $weekindex => $week ) {

		$params->setParam( 'week', $week['date'], PARAM_STRING );
		$week['prices'] = $Prices->find($params);
		array_push( $result->weeks, $week );
	}



	$result->supplies = $supplies;
	$result->providers = $providers;
	echo $result->toJson();
});



?>