<?php

/**
 * Clase para Compras
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
class Tienda
{

    /**
     * Retorna una lista de productos con base a las tiendas mas cercanas
     *
     * @param $lat float Latitud
     * @param $lng float Longitud
     * @param $range int Rango de busqueda
     * @param $unit string Unidad de medida
     * @return KoobenResponse
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function listaProductos($lat, $lng, $range, $unit ) {
        global $mysql;
        global $kooben;

        $lista = new KoobenResponse();
        $providers = new Model( 'providers', $mysql );
        $providers->setProperties( $kooben->models->providers );
        $suppliesPrices = new Model( 'suppliesPrices', $mysql );
        $suppliesPrices->setProperties( $kooben->models->supplies );
        $providerList = $providers->findAll();

        $me = [ 'lat' => $lat, 'lon' => $lng ];
        $range = floatval( $range );
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

        $lista->status = new GetModelStatus( ( count( $sqlList ) > 0 ), true );
        $lista->providers = $items;

        if ( $lista->status->found ) {

            $productList = $suppliesPrices->findBy( [
                'queryName' => 'get-shop',
                'params' => new QueryParams( [
                    'filterByProviders' => new QueryParamItem( 1 ),
                    'providers' => new QueryParamItem( implode( ',', $sqlList ) )
                ] ), 'devMode' => true
            ] );

            if ( $productList->status->found ) {
                $lista->status = $productList->status;
                $lista->items = $productList->items;
                $lista->proveedores = $sqlList;
            }
        }

        return $lista;
    }


    /**
     * Obtiene los mejores precios para los elementos de una lista de suministros.
     *
     * @param $items Lista de suministros
     * @return KoobenResponse
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function cotizar( $items )
    {
        global $mysql;
        global $kooben;

        $prices = new Model( 'suppliesPrices', $mysql );
        $prices->setProperties( $kooben->models->suppliesPrices );

        $cotizacion = new KoobenResponse();
        $cotizacion->items = array();

        foreach ( $items as $item_idx => $item ) {
            $search = $prices->findBy([
                'queryName' => 'shop-get-best-price-for-item',
                'params' => new QueryParams([
                    'supply' => new QueryParamItem( $item[ 'supplyId' ] ),
                    'mark' => new QueryParamItem( $item[ 'markId' ] ),
                    'presentation' => new QueryParamItem( $item[ 'presentationId' ] )
                ])
            ]);

            if ( $search->status->found ) {
                array_push( $cotizacion->items, $search->items[0] );
            }
        }

        $cotizacion->status = new GetModelStatus( count( $cotizacion->items ) > 0 );
        return $cotizacion;
    }
}