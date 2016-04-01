<?php
/**
* Shop deliverers routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/deliverers/:id/purchase', function( $id ) use( $mysql, $kooben ) {
	$purchases = new Model( 'purchases', $mysql );
	$purchases->setProperties( $kooben->models->purchases );

	$items = $purchases->findBy([
		'params' => new QueryParams([
			'delivererId' => new QueryParamItem( $id ),
			'delivered' => new QueryParamItem( 0 )
		])
	]);

	if ( !$items->status->found ) { echo createEmptyModelWithStatus( 'Get' )->toJson(); return; }

	$response = new KoobenResponse();
	$response->status = new GetModelStatus( true);

	$response->id = $items->items[0][ 'id' ];
	$response->street = $items->items[0][ 'street' ];
	$response->fullname = $items->items[0][ 'fullname' ];
	$response->number = $items->items[0][ 'number' ];
	$response->city = $items->items[0][ 'city' ];
	$response->code = $items->items[0][ 'code' ];
	$response->countryName = $items->items[0][ 'countryName' ];
	$response->state = $items->items[0][ 'state' ];
	$response->items = [];

	$items = new Model( 'purchasesItems', $mysql );
	$items->setProperties( $kooben->models->purchasesItems );
	$response->items = $items->findBy([
		'queryName' => 'get-with-detail',
		'params' => new QueryParams([
			'purchaseId' => new QueryParamItem( $response->id )
		])
	])->items;

	echo $response->toJson();
});

?>