<?php
/**
* Auth routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/shop/product-list/:latitude/:longitude/:range/:unit', function( $lat, $lon, $range, $unit ) use( $mysql, $kooben ) {
	$me = [ 'lat' => $lat, 'lon' => $lon ];

	$providers = new Model( 'providers', $mysql );
	$providers->setProperties( $kooben->models->providers );
	$suppliesPrices = new Model( 'suppliesPrices', $mysql );
	$suppliesPrices->setProperties( $kooben->models->supplies );
	$providerList = $providers->findAll();

	$result = new StdModel();
	$items = [];

	$sqlList = [];

	if ( $providerList->status->count > 0 ) {

		foreach ( $providerList->items as $provider_idx => $provider ) {

			if ( ( !is_null( $provider[ 'latitude' ] ) ) && ( !is_null( $provider[ 'longitude' ] ) ) ) {

				$target = [ 'lat' => $provider[ 'latitude' ], 'lon' => $provider[ 'longitude' ] ];
				$provider[ 'distance' ] = getDistanceBetweenTwoLocations( $me, $target, $unit );

				if ( $provider[ 'distance' ] <= $range ) {
					array_push( $sqlList, $provider[ 'id' ] );
					array_push( $items, $provider );
				}
			}
		}
	}

	$result->status = new GetModelStatus( ( count( $sqlList ) > 0 ), true );

	if ( $result->status->found ) {

		$productList = $suppliesPrices->findBy( [
			'queryName' => 'get-shop',
			'params' => new QueryParams( [
				'filterByProviders' => new QueryParamItem( 1 ),
				'providers' => new QueryParamItem( implode( ',', $sqlList ) )
			] ), 'devMode' => true
		] );

		if ( $productList->status->found ) {
			$result->status = $productList->status;
			$result->items = $productList->items;
		}
	}

	echo $result->toJson();
});




$app->post( '/shop/estimate', function() use ( $app, $mysql, $kooben ) {
	$post = $app->request->post();

	$prices = new Model( 'suppliesPrices', $mysql );
	$prices->setProperties( $kooben->models->suppliesPrices );

	$response = new KoobenResponse();
	$response->items = array();

	foreach ( $post[ 'items' ] as $item_idx => $item ) {
		$search = $prices->findBy([
			'queryName' => 'shop-get-best-price-for-item',
			'params' => new QueryParams([
				'supply' => new QueryParamItem( $item[ 'supplyId' ] ),
				'mark' => new QueryParamItem( $item[ 'markId' ] ),
				'presentation' => new QueryParamItem( $item[ 'presentationId' ] )
			])
		]);

		if ( $search->status->found ) {
			array_push( $response->items, $search->items[0] );
		}
	}

	$response->status = new GetModelStatus( count( $response->items ) > 0 );
	echo $response->toJson();
});

?>