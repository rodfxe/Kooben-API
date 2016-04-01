<?php
/**
* Session routes for Kooben
*
* Created - Martin Samuel Esteban Diaz
*/


$app->get( '/sessions/:id/validate', function($id) use($mysql, $kooben){
    $sessions = new Model( 'sessions', $mysql );
    $sessions->setProperties( $kooben->models->sessions );
    $check = $sessions->findById( 'hash', $id );

    $session = createEmptyModelWithStatus( 'Get' );
    $session->status->valid = $check->status->found;
    $session->name = $check->username;
    unset($session->status->found);

    echo $session->toJson();
});




$app->post( '/sessions', function() use($app, $mysql, $kooben){
    $sessions = new Model( 'sessions', $mysql );
    $sessions->setProperties( $kooben->models->sessions );

    $values = $app->request->post();
    $values[ 'application' ] = $app->request->headers->get('KOOBEN-APPLICATION-NAME');
    $valForHash = ( rand() . $values[ 'username' ] . date( 'Y-m-d H:i:s', time() ) );

    $sessions->hash = hash( 'md5', $valForHash );
    $sessions->username = $values[ 'username' ];
    $sessions->application = $values[ 'application' ];
    $sessions->type = 'free';
    $sessions->userId = -1;

    $session = $sessions->create();
    if( $session->status->created ){
        $session->id = $sessions->hash;
        $session->name = $values[ 'username' ];
    }

    echo $session->toJson();
});

?>
