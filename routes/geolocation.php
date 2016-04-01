<?php
 header("Access-Control-Allow-Origin: *");
/**
* Consults for prices routes for Kooben
* 
* Created - Martin Samuel Esteban Diaz
*/ 
$app->get( '/user-geolocation/:latitude/:longitude/:kilometers', function( $latitude, $longitude,$kilometers) use($mysql, $kooben){
	# create result
	$result = new StdModel();
 
	# model for geolocation
      
	$geolocation_companies = new Model( 'enterprise', $mysql );
	$geolocation_companies->setProperties( $kooben->models->enterprise);
	$geolocation_companies->useQuery( 'get-the-closest-companies' );
	# filter for geolocation
	$filter_geolocation_companies = new QueryParams();
	$filter_geolocation_companies->setParam( 'lat', $latitude, PARAM_STRING );
	$filter_geolocation_companies->setParam( 'lng', $longitude, PARAM_STRING );
	$filter_geolocation_companies->setParam( 'kilometers', $kilometers ); 
	$result = $geolocation_companies->find($filter_geolocation_companies);

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