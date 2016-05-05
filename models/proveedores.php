<?php
/**
* Clase para Proveedores
* 
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/
class Proveedores extends Model
{

	function __construct()
	{
		global $mysql;
		global $kooben;

		parent::__construct( 'providers', $mysql );
		parent::setProperties( $kooben->models->providers );
	}


	/**
	 * Retorna una lista completa de proveedores
	 * 
	 * @return KoobenResponse
	 * @author Martin Samuel Esteban Diaz <edmsamuel>
	 */
	public static function all() {
		global $mysql;
		global $kooben;

		$proveedores = new Model( 'providers', $mysql );
        $proveedores->setProperties( $kooben->models->providers );
        return $proveedores->findAll();
	}
}