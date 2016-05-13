<?php
/**
 * Kooben
 *
 * @author Martin Samuel Esteban Diaz
 */
class Kooben
{
    public $config;
    public $core;
    public $definitions;
    public $fileSizes;
    public $models;
    public $routes;
    public $uploadFolder;

    function __construct()
    {
        $config = json_decode( file_get_contents( 'config.json' ) );
        $this->config = $config->config;
        $this->core = $config->core;
        $this->uploadFolder = $config->uploadFolder;
        $this->definitions = $config->definitions;
        $this->fileSizes = $config->fileSizes;
        $this->routes = $config->routes;

        $this->models = new stdClass();

        foreach ( $this->definitions as $definition ) {
            $this->models->$definition = json_decode( file_get_contents( "definitions/$definition.json" ) );
        }
    }


    /**
     * Retorna el nombre de tabla de un modelo
     *
     * @param $model
     * @return string
     */
    public function getTableNameOf( $model ) {
        return $this->models->$model->__table_name__;
    }
}