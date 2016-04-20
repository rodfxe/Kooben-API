<?php

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