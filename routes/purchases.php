<?php
/**
* Purchase routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/purchases', function() use( $app, $mysql, $kooben ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) { echo createEmptyModelWithStatus( 'Get' )->toJson(); return; }

	$purchases = new Model( 'purchases', $mysql );
	$purchases->setProperties( $kooben->models->purchases );
	$products = new Model( 'purchasesItems', $mysql );
	$products->setProperties( $kooben->models->purchasesItems );

	$response = $purchases->findBy([
		'params' => new QueryParams([
			'userId' => new QueryParamItem( $session->userId ) ])
	]);

	if ( $response->status->found ) {
		foreach ( $response->items as $purchase_idx => $purchase ){
			$response->items[ $purchase_idx ][ 'products' ] = $products->findBy([
				'params' => new QueryParams([
					'purchaseId' => new QueryParamItem( $purchase[ 'id' ] )
				])
			]);
		}
	}

	echo $response->toJson();
});




$app->get( '/purchases/:id', function( $id ) use( $mysql, $kooben ) {
	$purchases = new Model( 'purchases', $mysql );
	$purchases->setProperties( $kooben->models->purchases );
	echo $purchases->findById( '__default__', $id )->toJson();
});




$app->get( '/purchases/:id/items', function( $id ) use( $mysql, $kooben ) {
	$items = new Model( 'purchasesItems', $mysql );
	$items->setProperties( $kooben->models->purchasesItems );
	echo $items->findBy([
		'params' => new QueryParams([
			'purchaseId' => new QueryParamItem( $id )
		])
	])->toJson();
});





$app->post( '/purchases', function() use( $app, $mysql, $kooben ) {
	$session = checkKoobenSession( $app );
	$response = createEmptyModelWithStatus( 'Post' );
	$delivererId = -1;
	$invalidItems = ( count( $app->request->post( 'items' ) ) == 0 );

	if ( !$session->status->found ) { echo $response->toJson(); return; }
	if ( $invalidItems ){ $response->status->validItems = false; echo $response->toJson(); return; }

	# search a delivery man
	$deliveries = new Model( 'shopDeliverers', $mysql );
	$deliveries->setProperties( $kooben->models->shopDeliverers );
	$delivererList = $deliveries->findBy([
		'params' => new QueryParams([
			'free' => new QueryParamItem( 1 )
		])
	]);

	if ( $delivererList->status->found ) {
		$deliverer = $delivererList->items[ 0 ];
		$delivererId = $deliverer[ 'id' ];

		$reserver = new Query( $mysql );
		$reserver->type = QUERY_UPDATE;
		$reserver->setSql( "update shop_deliverers set free = 0 where id = $delivererId;" );
		$reserver->execQuery();
	} else {
		$response->status->delivererNotFound = true; echo $response->toJson(); return;
	}

	$values = $app->request->post();

	$purchases = new Model( 'purchases', $mysql );
	$purchases->setProperties( $kooben->models->purchases );
	$purchases->delivererId = $delivererId;
	$purchases->addressId = $values[ 'addressId' ];
	$response = $purchases->create();

	if ( $response->status->created ) {
		saveInKoobenKardex( $response->id, $session->id, $purchases->tableName() );

		$purchaseItems = array();
		$item = array();
		foreach ( $values[ 'items' ] as $item_idx => $itemValues ) {
			array_push( $purchaseItems, array(
				$response->id,
				$itemValues[ 'priceId' ],
				$itemValues[ 'cant' ]
			) );
		}

		$items = new Model( 'purchasesItems', $mysql );
		$items->setProperties( $kooben->models->purchasesItems );

		$response->items = $items->multiCreate( array(
			'items' => $purchaseItems,
			'rules' => array( PARAM_INT, PARAM_INT, PARAM_INT ),
			'getQueryName' => 'get-with-detail',
			'getQueryParams' => new QueryParams([
				'purchaseId' => new QueryParamItem( $response->id )
			])
		));
	}

	echo $response->toJson();
});





$app->put( '/purchases/:id/set-delivered', function( $id ) use( $app, $mysql, $kooben ) {
	$session = checkKoobenSession( $app );
	if ( !$session->status->found ) { echo createEmptyModelWithStatus( 'Get' )->toJson(); return; }

	$purchases = new Model( 'purchases', $mysql );
	$purchases->setProperties( $kooben->models->purchases );
	$purchases->useQuery( 'set-delivered' );
	$purchases->id = $id;
	$response = $purchases->save();

	if ( $response->status->updated ) { saveInKoobenKardex( $id, $session->id, $purchases->tableName(), 'update' ); }
	echo $response->toJson();
});


?>