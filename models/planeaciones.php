<?php
/**
 * Planeacion Recetas
 *
 * @author Martin Samuel Esteban Diaz
 */
class PlaneacionRecetas extends Model
{

    function __construct()
    {
        global $mysql;
        global $kooben;

        parent::__construct( 'planeacionRecetas', $mysql );
        parent::setProperties( $kooben->models->planeacionRecetas );
    }
}



/**
* Planeacion Dias
* 
* @author Martin Samuel Esteban Diaz
*/
class PlaneacionDias extends Model
{
	
	function __construct()
	{
		global $mysql;
		global $kooben;

		parent::__construct( 'planeacionDias', $mysql );
		parent::setProperties( $kooben->models->planeacionDias );
	}
}



/**
 * Planeaciones
 *
 * @author Martin Samuel Esteban Diaz
 */
class Planeacion extends Model
{

    function __construct()
    {
        global $mysql;
        global $kooben;

        parent::__construct( 'planeaciones', $mysql );
        parent::setProperties( $kooben->models->planeaciones );
    }



    /**
     * Obtiene los días de una planeación
     *
     * @param $id int Id de planeación
     * @return KoobenResponse resultado de la busqueda
     * @author Martin Samuel Esteban Diaz <edmsamuel>
    */
    public static function obtenerDias( $id = -1 ) {
        $dias = new PlaneacionDias();
        return $dias->findBy([
            'params' => new QueryParams([
                'planeacion' => new QueryParamItem( $id )
            ])
        ]);
    }



    /**
     * Obtiene las recetas de una planeación
     *
     * @param $id int Id de planeación
     * @return KoobenResponse resultado de la busqueda.
     * @author Martin Samuel Esteban Diaz <edmsamuel>
    */
    public static function obtenerRecetas( $id = -1 ) {
        $recetas = new PlaneacionRecetas();
        return $recetas->findBy([
            'queryName' => 'get-with-detail',
            'params' => new QueryParams([
                'planeacion' => new QueryParamItem( $id )
            ])
        ]);
    }



    /**
     * Obtener el resumen de una planeación
     *
     * @param $planeacionId int Id de planeación
     * @return KoobenResponse Resultado de la planeación.
     * @author Martin Samuel Esteban Diaz <edmsamuel>
     */
    public static function obtenerResumen( $planeacionId = -1, $version, $lat, $lng, $rng, $unt ) {
        $dias = new PlaneacionDias();
        $recetas = new PlaneacionRecetas();
        $resumen = new KoobenResponse();

        $proveedores = implode( ',', Geolocalizacion::listaProveedoresId( $lat, $lng, $rng, $unt ) );

        $resumen->dias = $dias->findBy([ 'params' => new QueryParams([
            'planeacion' => new QueryParamItem( $planeacionId )
        ]) ]);

        $resumen->suministros = $recetas->findBy([
            'queryName' => 'get-suministros',
            'params' => new QueryParams([
                'planeacion' => new QueryParamItem( $planeacionId ),
                'filtrarPorCoordenadas' => new QueryParamItem( 1 ),
                'proveedores' => new QueryParamItem( $proveedores )
            ])
        ]);


        $cantidades = $recetas->findBy([
            'queryName' => "get-resumen-$version",
            'params' => new QueryParams([
                'planeacion' => new QueryParamItem( $planeacionId )
            ])
        ]);

        if ( !$resumen->suministros->status->found ) {
            echo createEmptyModelWithStatus('Get')->toJson(); return;
        }

        foreach ( $resumen->dias->items as $dia_idx => $dia ) {
            $resumen->dias->items[ $dia_idx ][ 'total' ] = 0.0;
        }

        foreach ( $resumen->suministros->items as $suministro_idx => $suministro) {
            unset( $resumen->suministros->items[ $suministro_idx ][ 'planeacionId' ] );
            $resumen->suministros->items[ $suministro_idx ][ 'cantidades' ] = [];

            foreach ($resumen->dias->items as $dia_idx => $dia) {
                unset( $resumen->dias->items[ $dia_idx ][ 'planeacionId' ] );
                $founded = false;

                foreach ( $cantidades->items as $cantidad_idx => $cantidad ) {

                    if ( $cantidad[ 'suministroId' ] == $suministro[ 'id' ] && $cantidad[ 'diaId' ] == $dia[ 'id' ] ) {
                        $founded = true;
                        array_push( $resumen->suministros->items[ $suministro_idx ][ 'cantidades' ], [
                            'val' => $cantidad[ 'cantidadSuministro' ],
                            'unidad' => $cantidad[ 'nombreUnidad' ],
                            'importe' => floatval( $cantidad[ 'importe' ] ),
                            'diaId' => $cantidad[ 'diaId' ],
                            'precioId' => $cantidad[ 'precioId' ]
                        ] );
                        $resumen->dias->items[ $dia_idx ][ 'total' ] += floatval( $cantidad[ 'importe' ] ); break;
                    }
                }

                if ( !$founded ){ array_push( $resumen->suministros->items[ $suministro_idx ][ 'cantidades' ], [
                    'val' => empty_str,
                    'unidad' => empty_str,
                    'importe' => 0.0,
                    'diaId' => -1,
                    'precioId' => -1
                ] ); }

            }
        }

        return $resumen;
    }


    /**
     * Cotización creada con base a la presentación de menor precio
     *
     * @param $planeacionId
     * @return KoobenResponse
     */
    public static function cotizacion( $planeacionId, $tipo, $lat, $lng, $rng ) {
        global $mysql;

        $resumen = self::obtenerResumen( $planeacionId, $tipo, $lat, $lng, $rng, 'K' );
        $resumen->total = 0.0;
        foreach ( $resumen->suministros->items as $suministro_idx => $suministro ) {
            $resumen->suministros->items[ $suministro_idx ][ 'importeTotal' ] = 0;

            foreach ( $suministro[ 'cantidades' ] as $cantidad_idx => $cantidad ) {
                $resumen->suministros->items[ $suministro_idx ][ 'importeTotal' ] += $cantidad[ 'importe' ];
            }

            $resumen->total += $resumen->suministros->items[ $suministro_idx ][ 'importeTotal' ];
        }
        return $resumen;
    }
}