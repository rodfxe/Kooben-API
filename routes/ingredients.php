<?php
/**
* Ingredients routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/

# get all ingredients.
$app->get( '/ingredients', function() use ( $app, $mysql, $kooben){
    $ingredients = new Model( 'ingredients', $mysql );
    $ingredients->setProperties( $kooben->models->ingredients );
    echo $ingredients->findAll()->toJson();
});















# get an ingredient.
$app->get( '/ingredients/:id', function($id) use ($mysql, $kooben){
    $ingredients = new Model( 'ingredients', $mysql );
    $ingredients->setProperties( $kooben->models->ingredients );

    echo $ingredients->findById( 'IdRecetaPartida', intval( $id ) )->toJson();
});

















# create a ingredient into one recipe
$app->post( '/ingredients/recipe/:id', function($id) use ( $app, $mysql, $kooben, $sessions, $kardex ){
    $hash = $app->request->headers->get( 'KOOBEN-SESSION-ID' );
    $session = $sessions->findById( 'hash', $hash );

    if ( $session->status->found ){

        $ingredients = new Model( 'ingredients', $mysql );
        $ingredients->setProperties( $kooben->models->ingredients );

        $values = $app->request->post();
        $ingredient = $ingredients->newItem();
        $ingredient->IdReceta = intval( $id );
        $ingredient->IdInsumo = intval( $values[ 'supply' ] );
        $ingredient->iIdUnidad = intval( $values[ 'measure' ] );
        $ingredient->Cantidad = floatval( $values[ 'cant' ] );
        $newRecord = $ingredient->create();

        if ( $newRecord->status->created ){
            $registry = $kardex->newItem();
            $registry->rowId = $newRecord->id;
            $registry->sessionId = $session->realId;
            $registry->tableName = 'cmt_recetapartida';
            $registry->operation = 'insert';
            $registry->create();
        }

        echo $newRecord->toJson();
    } else {
        $result = new StdModel();
        $result->status = new PostModelStatus();

        echo $result->toJson();
    }
});




















$app->put( '/ingredients/:id', function($id) use($app, $mysql, $kooben){
    $id = intval( $id );
    $session = checkKoobenSession( $app );

    if( !$session->status->found ){
        $result = new StdModel();
        $result->status = new PutModelStatus();

        echo $result->toJson();
        return;
    }

    $ingredients = new Model( 'ingredients', $mysql );
    $ingredients->setProperties( $kooben->models->ingredients );

    $ingredient = $ingredients->findById( 'IdRecetaPartida', $id );
    if( !$ingredient->status->found ){
        echo $ingredient->toJson();
        return;
    }


    $result = new StdModel();
    $values = $app->request->put();
    $ingredient->IdRecetaPartida = $id;
    $ingredient->IdInsumo = intval( $values[ 'supply' ] );
    $ingredient->iIdUnidad = intval( $values[ 'measure' ] );
    $ingredient->Cantidad = floatval( $values[ 'cant' ] );
    $result->status = $ingredient->save();


    if( $result->status->updated ){
        saveInKoobenKardex( intval($id), $session->id, 'cmt_recetapartida', 'update' );
    }

    echo $result->toJson();
});

























$app->delete( '/ingredients/:id', function($id) use($app, $mysql, $kooben){
    $id = intval($id);
    $session = checkKoobenSession( $app );
    if ( !$session->status->found ){
        $result = new StdModel();
        $result->status = new DeleteModelStatus();
        echo $result->toJson();
        return;
    }

    $ingredients = new Model( 'ingredients', $mysql );
    $ingredients->setProperties( $kooben->models->ingredients );
    $status = $ingredients->delete( $id );

    if ( $status->deleted ){
        saveInKoobenKardex( $id, $session->id, 'cmt_recetapartida', 'delete' );
    }

    $ingredient = new StdModel();
    $ingredient->status = $status;

    echo $ingredient->toJson();
});



?>
