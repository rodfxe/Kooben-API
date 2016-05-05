<?php
/**
* Rutas para Compras
*
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/

/**
 * Retorna una lista de productos mas cercanos a las coordenadas recibidas
 *
 * @param $lat float Latitud de coordenadas
 * @param $lng float Longitud de coordenadas
 * @param $range float Rango de busqueda
 * @param $unit Unidad de busqueda
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/shop/product-list/:latitude/:longitude/:range/:unit', function( $lat, $lng, $range, $unit ) {
	echo Tienda::listaProductos( $lat, $lng, $range, $unit )->toJson();
});



/**
 * Retorna la cotizacion de una lista de productos
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->post( '/shop/estimate', function() use ( $app ) {
	echo Tienda::cotizar( $app->request->post()[ 'items' ] )->toJson();
});

?>