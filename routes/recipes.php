<?php
/**
* Recipes routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get all recipes.
$app->get( '/recipes', function() use ( $app, $mysql, $kooben){
    $recipes = new Model( 'recipes', $mysql );
    $recipes->setProperties( $kooben->models->recipes );
    echo $recipes->findAll()->toJson();
});






# get an recipe.
$app->get( '/recipes/:id', function($id) use ($mysql, $kooben){
    $recipes = new Model( 'recipes', $mysql );
    $recipes->setProperties( $kooben->models->recipes );
    $recipes->useQuery( 'single' );
    echo $recipes->findById( 'IdReceta', intval( $id ) )->toJson();
});






# get ingredients of an recipe.
$app->get( '/recipes/:id/ingredients', function($id) use ($mysql, $kooben){
    $recipes = new Model( 'recipes', $mysql );
    $recipes->setProperties( $kooben->models->ingredients );
    $recipes->useQuery( 'ingredients' );
    $filter = new QueryParams;
    $filter->setParam( 'IdReceta', intval( $id ) );
    echo $recipes->find( $filter )->toJson();
});





# get recipes filtered.
$app->get( '/recipes/filter/:keywords', function($keywords) use ($mysql, $kooben){
    $recipes = new Model( 'recipes', $mysql );
    $recipes->setProperties( $kooben->models->recipes );
    $recipes->useQuery( 'filter' );
    $filter = new QueryParams;
    $filter->setParam( 'keywords', $keywords, PARAM_STRING );
    echo $recipes->find( $filter )->toJson();
});

?>
