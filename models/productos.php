<?php

/**
 * Producto
 *
 * @author Martin Samuel Esteban Diaz <edmsamuel>
 */
class Producto
{

    /**
     * Retorna una lista de precios
     *
     * @param $productoId int Id de producto
     * @param $marcaId int Id de marca
     * @return KoobenResponse
     * 
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function precios($productoId, $marcaId = -1 ) {
        global $kooben;
        global $mysql;

        $precios = new Model( 'suppliesPrices', $mysql );
        return $precios->findBy([
            'queryName' => 'get precios captura',
            'params' => new QueryParams([
                'producto' => new QueryParamItem( $productoId ),
                'marca' => new QueryParamItem( $marcaId )
            ])
        ]);
    }


    /**
     * Crea un nuevo precio
     *
     * @param $datos array Datos del nuevo precio
     * @return Model
     *
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function crearPrecio($datos ) {
        global $kooben;
        global $mysql;

        $precios = new Model( 'suppliesPrices', $mysql );
        $precios->setProperties( $kooben->models->suppliesPrices );
        $precios->setValuesFromArray( $datos );

        return $precios->create();
    }


    /**
     * Crea una nueva asignación de marca a producto
     *
     * @param $datos array Datos de la nueva asignación
     * @return Model
     *
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function asignarMarca($datos ) {
        global $kooben;
        global $mysql;

        $marcas = new Model( 'suppliesMarks', $mysql );
        $marcas->setProperties( $kooben->models->suppliesMarks );
        $marcas->setValuesFromArray( $datos );

        return $marcas->create();
    }
}