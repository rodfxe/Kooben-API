<?php
/**
* Clase para Geolocalización
* 
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
class Geolocalizacion
{


	/**
	 * Obtiene lista de proveedores
	 * Retorna una lista de proveedores con base a las coordenadas proporcioandas.
	 * 
	 * @param $lat float Latitud
	 * @param $lng float Longitud
	 * @param $rng float Rango de busqueda
	 * @param $unt string Unidad de medida
	 * @return KoobenResponse
	 * 
	 * @author Martin Samuel Esteban Diaz <edmsamuel>
	*/
	public static function proveedores( $lat, $lng, $rng, $unt ) {
		global $mysql;
		global $kooben;

		$lista = new KoobenResponse();
		$proveedores = Proveedores::all();

		$me = [ 'lat' => $lat, 'lon' => $lng ];
		$rng = floatval( $rng );
		$lista->items = [];
		$count = 0;

		if ( $proveedores->status->count > 0 ) {

            foreach ( $proveedores->items as $provider_idx => $provider ) {

                if ( ( !is_null( $provider[ 'latitude' ] ) ) && ( !is_null( $provider[ 'longitude' ] ) ) ) {

                    $target = [ 'lat' => $provider[ 'latitude' ], 'lon' => $provider[ 'longitude' ] ];
                    $provider[ 'distance' ] = getDistanceBetweenTwoLocations( $me, $target, $unt );

                    if ( $provider[ 'distance' ] <= $rng ) {
                        array_push( $lista->items, $provider );
                        $count++;
                    }
                }
            }
        }

        $lista->status = new GetModelStatus( $count > 0 );
        $lista->status->count = $count ;
        unset( $lista->status->valid );
        return $lista;
	}


    /**
     * Obtiene solo la lista de id de proveedores
     *
     * Retorna un arreglo con los id de los proveedores
     * encontrados con base a la ubicación.
     *
     * @param $lat float Latitud
     * @param $lng float Longitud
     * @param $rng float Rango de busqueda
     * @param $unt string Unidad de medida
     * @return array
     *
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function listaProveedoresId( $lat, $lng, $rng, $unt ) {
        $lista = [];
        $proveedores = self::proveedores( $lat, $lng, $rng, $unt );

        if ( $proveedores->status->found ) {
            foreach ( $proveedores->items as $item_idx => $proveedor ) {
                array_push( $lista, $proveedor[ 'id' ] );
            }
        }

        return $lista;
    }


    /**
     * @param $producto Id de producto
     * @param $proveedores Lista de id's de proveedores
     * @return StdModel
     *
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function marcasProducto($producto, $proveedores ) {
        global $mysql;
        $marcas = new Model( 'marks', $mysql );

        return $marcas->findBy([
            'queryName' => 'get-shop-marks',
            'params' => new QueryParams([
                'supply' => new QueryParamItem( $producto ),
                'filterByProviders' => new QueryParamItem( 1 ),
                'providers' => new QueryParamItem( $proveedores )
            ])
        ]);
    }
}