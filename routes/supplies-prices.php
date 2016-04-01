<?php
/**
* Prices of supplies routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# create a new price
$app->post( '/supplies/:supply/prices', function($supply) use($app, $mysql, $kooben){
	$supply = intval($supply);
	$session = checkKoobenSession($app);
	if ( !$session->status->found ){
		$result = new StdModel();
        $result->status = new PostModelStatus();

        echo $result->toJson();
		return;
	}

	$values = $app->request->post();
	$prices = new Model( 'suppliesPrices', $mysql );
	$prices->setProperties( $kooben->models->suppliesPrices );
	$prices->IdInsumo = $supply;
	$prices->iIdEmpresa = intval($values['provider']);
	$prices->IdMarca = intval($values['mark']);
	$prices->IdPresentacion = intval($values['presentation']);
	$prices->PrecioCompra = floatval($values['buyPrice']);
	$prices->PrecioVenta = floatval($values['salePrice']);
	$prices->Aplicacion = $values['application'];
	$price = $prices->create();

	if ($price->status->created){
		saveInKoobenKardex( $price->id, $session->id, 'cmt_insumoprecio' );
	}

	echo $price->toJson();
});

?>