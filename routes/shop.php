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
 * Retorna una lista de marcas correspondientes a un producto
 *
 * @param $supply int Id de producto
 * @param $providers string Lista de id's de proveedores
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
$app->get( '/shop/supply/:supply/marks/:providers', function( $supply, $providers ) {
	echo Geolocalizacion::marcasProducto( $supply, $providers )->toJson();
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