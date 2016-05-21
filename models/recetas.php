<?php
/**
* Recipes
* Clase para recetas.
*/
class Recipes extends Model
{
	function __construct()
	{
		global $kooben;
		global $mysql;

		parent::__construct( 'recipes', $mysql );
		parent::setProperties( $kooben->models->recipes );
	}
}

/**
* Recetas
* Clase para las recetas.
* 
* @author edmsamuel
*/
class Receta
{

	public static function lista() {
		$recetas = new Recipes();
		return $recetas->findAll();
	}



	public static function byId( $id ) {
		$recetas = new Recipes();
		$recetas->useQuery( 'single' );
		return $recetas->findById( '__default__', $id );
	}



	public static function pagina( $from, $cantidad ) {
		$recetas = new Recipes();
		return $recetas->findBy([
			'queryName' => 'pages',
			'params' => new QueryParams([
				'desde' => new QueryParamItem( $from ),
				'cantidad' => new QueryParamItem( $cantidad )
			])
		]);
	}



	public static function ingredientes( $receta ) {
		global $mysql;
		global $kooben;

		$ingredientes = new Model( 'ingredients', $mysql );
		$ingredientes->setProperties( $kooben->models->ingredients );
		return $ingredientes->findBy([
			'params' => new QueryParams([
				'IdReceta' => new QueryParamItem( $receta )
			])
		]);
	}



	public static function buscar( $keywords ) {
		$recetas = new Recipes();
		return $recetas->findBy([
			'queryName' => 'filter',
			'params' => new QueryParams([
				'keywords' => new QueryParamItem( $keywords, QueryParamItem::TYPE_STR )
			])
		]);
	}
}