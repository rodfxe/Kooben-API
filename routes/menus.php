<?php
/**
* Menu routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

$app->get( '/menus', function() use( $app, &$mysql, $kooben ){
	$params = new QueryParams();
	$menus = new Model( 'menus', $mysql );
	$menus->setProperties( $kooben->models->menus );
	$params->setParam( 'IdSession', $app->request->headers->get( 'KOOBEN-SESSION-ID' ), PARAM_STRING );
	$result = $menus->find( $params );

	if ( $result->status->found ){
		$days = new Model( 'registryConsumption', $mysql );
		$days->setProperties( $kooben->models->registryConsumption );
		foreach ( $result->items as $menu_idx => $menu ){
			$menuItems = $days->find( new QueryParams( array(
				'IdMenu' => new QueryParamItem( $menu['id'] )
			) ) );

			$result->items[ $menu_idx ]['days'] = $menuItems->items;
		}
	}

	echo $result->toJson();
});








$app->get( '/menus/:menuId', function( $menuId ) use( $app, $mysql, $kooben ){
	$menuId = intval($menuId);
	$menus = new Model( 'menus', $mysql );
	$menus->setProperties( $kooben->models->menus );

	echo $menus->findById( '__default__', $menuId )->toJson();
});






$app->get( '/menus/:menuId/resume', function( $menuId ) use( $app, &$mysql, $kooben ){

	$menuId = intval($menuId);
	$Menus = new Model( 'menus', $mysql );
	$Menus->setProperties( $kooben->models->menus );

	$Days = new Model( 'registryConsumption', $mysql );
	$Days->setProperties( $kooben->models->registryConsumption );

	$Recipes = new Model( 'registryConsumptionData', $mysql );
	$Recipes->setProperties( $kooben->models->registryConsumption );

	$PeriodConsumption = new Model( 'periodConsumption', $mysql );
	$PeriodConsumption->setProperties( $kooben->models->periodConsumption );

	$Ingredients = new Model( 'ingredients', $mysql );
	$Ingredients->setProperties( $kooben->models->ingredients );

	$result = new StdModel();
	$result->days = array();
	$result->menu = $Menus->findById( '__default__', $menuId );

	$days = $Days->findBy( array(
		'params' => new QueryParams( array(
			'IdMenu' => new QueryParamItem( $menuId )
		) )
	) );

	$ingredients = $Ingredients->findBy( array(
		'params' => new QueryParams( array(
			'menuId' => new QueryParamItem( $menuId )
		) ),
		'queryName' => 'menu-recipe'
	) );

	$ingredientsDay = $Ingredients->findBy( array(
		'params' => new QueryParams( array(
			'menuId' => new QueryParamItem( $menuId )
		) ),
		'queryName' => 'menu-day'
	) );

	$result->ingredientsDay = $ingredientsDay->items;

	$recipes = $Recipes->findBy( array(
		'params' => new QueryParams( array(
			'IdMenu' => new QueryParamItem( $menuId )
		) )
	) );

	$ingredientCount = 0;

	if ( $days->status->found ){

		if ( $recipes->status->found ){

			# loop for the days
			foreach ( $days->items as $day_idx => $day ){

				$days->items[$day_idx]['recipes'] = array();
				$days->items[$day_idx]['ingredients'] = array();
				$days->items[$day_idx]['recipeCount'] = 0;
				foreach ( $recipes->items as $recipe_idx => $recipe ){
					# search the recipes of the day
					if ( $recipe['registryConsumption'] == $day['id'] ){

						$ingredientCount = 0;
						foreach ( $ingredients->items as $ingredient_idx => $ingredient ){

							if ( $ingredient['recipeId'] == $recipe['recipeId'] ){
								array_push( $days->items[$day_idx]['ingredients'], $ingredient );
								$ingredientCount++;
							}

						}

						if ( $ingredientCount > 0 ){
							$recipe['ingredients'] = $ingredientCount;
							array_push( $days->items[$day_idx]['recipes'], $recipe );
							$days->items[$day_idx]['recipeCount']++;
						}
					}

				}

			}

			# set the final days to the result.
			foreach ( $days->items as $day_idx => $day ){
				if ( $day['recipeCount'] > 0 ){
					array_push( $result->days, $day );
				}
			}

		}

	}


	echo $result->toJson();
});

/*
# estructure of the result.

Menu:
{
	Days:
	[
		Day:
		{
			Recipes:
			[
				Recipe:
				{
					Ingredients:
					[
						Ingredient:
						{
						}
					]
				}
			]
		}
	]
}

*/













$app->post( '/menus', function() use($app, $mysql, $kooben){
	$session = checkKoobenSession($app);
	if ( !$session->status->found ){ echo createEmptyModelWithStatus( 'Post', KOOBEN_SESSION_INVALID ); return; }

	$menus = new Model( 'menus', $mysql );
	$menus->setProperties( $kooben->models->menus );
	$menus->IdSession = $session->id;
	$menus->setCustomTypeForProperty( 'IdSession', 'STR' );
	$menu = $menus->setValuesFromArray( $app->request->post() )->create();



	if( $menu->status->created ){
		saveInKoobenKardex( $menu->id, $session->id, $menus->tableName() );

		$created = 0;
		$menuId = $menu->id;
		$menus = new Model( 'menus', $mysql );
		$menus->setProperties( $kooben->models->menus );

		$consumptions = new Model( 'registryConsumption', $mysql );
		$consumptions->setProperties( $kooben->models->registryConsumption );
		$consumptions->IdMenu = $menuId;
		$consumptions->Personas = 1;

		$result = $menus->findById( '__default__', $menuId );
		$result->status = $menu->status;
		$result->id = $menuId;
		$result->code = $menu->CodigoMenu;
		$result->days = array();

		$start = new DateTime( $result->from, new DateTimeZone( 'America/Mexico_City' ) );
		$interval = new DateInterval( 'P1D' );

		for ( $day = 0; $day < 7; $day++ ){
			
			$date = $start->format( 'Y-m-d' );
			$consumptions->Fecha = $date;
			$consumption = $consumptions->create();

			if ( $consumption->status->created ){
				$created++;
				$result->days[$day] = array(
					'id' => $consumption->id,
					'day' => $date
				);
			}
			unset($consumption);
			$start->add( $interval );
		}

		echo $result->toJson();
	} else {
		echo $menu->toJson();
	}

});

$app->get( '/interval/:date', function($date){
	$interval = new DateInterval( 'P7D' );
	$fecha = new DateTime( $date, new DateTimeZone( 'America/Mexico_City' ) );

	echo json_encode( $fecha->add( $interval ) );
});














$app->put( '/menus/:id', function($id) use($app, $mysql, $kooben){
	$session = checkKoobenSession($app);
	if ( !$session->status->found ){ echo createEmptyModelWithStatus( 'Put', KOOBEN_SESSION_INVALID ); return; }
	$id = intval($id);

	$menus = new Model( 'menus', $mysql );
	$menus->setProperties( $kooben->models->menus );
	$menus->IdMenu = $id;

	$menu = new StdModel();
	$menu->status = $menus->setValuesFromArray( $app->request->put() )->save();

	if( $menu->status->updated ){ saveInKoobenKardex( $menu->id, $session->id, $menu->tableName() ); }
	echo $menu->toJson();
});






/* PERIODS */

$app->get( '/menus/:menuId/periods', function($menuId) use( $mysql, $kooben ){
	$menuId = intval($menuId);
	$periods = new Model( 'menuPeriods', $mysql );
	$periods->setProperties( $kooben->models->periods );

	$filter = new QueryParams();
	$filter->setParam( 'IdMenu', $menuId );

	echo $periods->find( $filter )->toJson();
});






# PERIODS DATA

$app->get( '/menus/:menuId/periods/recipes', function($menuId) use( $mysql, $kooben ){
	$recipes = new Model( 'periodRecipes', $mysql );
	$recipes->setProperties( $kooben->models->periodRecipes );

	$filter = new QueryParams();
	$filter->setParam( 'IdMenu', $menuId );

	echo $recipes->find( $filter )->toJson();
});






$app->get( '/menus/:menuId/periods/:periodId/recipes', function($menuId, $periodId) use( $mysql, $kooben ){
	$recipes = new Model( 'periodRecipes', $mysql );
	$recipes->setProperties( $kooben->models->periodRecipes );

	$filter = new QueryParams();
	$filter->setParam( 'IdMenu', $menuId );
	$filter->setParam( 'IdPeriod', $periodId );

	echo $recipes->find( $filter )->toJson();
});








?>