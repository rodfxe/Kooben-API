<?php
/**
* API para Kooben
*
* Iniciado el 2015-10-10 : 21:00:00 hrs
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/


/* Get the general configuration */
$kooben = json_decode( file_get_contents( 'config.json' ) );

/* Import the core */
include $kooben->core->query;
include $kooben->core->model;

require $kooben->core->slim;
\Slim\Slim::registerAutoloader();


/* Create the MySQL connection */
$mysqlprofile = $kooben->config->mysql->profile;
$mysqlparameters = $kooben->config->mysql->$mysqlprofile;

$mysql = new mysqli(
    $mysqlparameters->host,
    $mysqlparameters->user,
    $mysqlparameters->password,
    $mysqlparameters->database,
    $mysqlparameters->port
);

$mysql->set_charset( 'utf8' );

/* Create kardex */
$kardex = new Model( 'kardex', $mysql );
$kardex->setProperties( $kooben->models->kardex );

/* Create session */
$sessions = new Model( 'sessions', $mysql );
$sessions->setProperties( $kooben->models->sessions );


# import generic methods
include $kooben->core->generics;

# import the core messages.
include $kooben->core->messages;

# import the core upload.
include $kooben->core->upload;


/* Instantiate a Slim application */
$app = new \Slim\Slim();
$app->response()->header( 'Content-Type', 'application/json' );
$app->response()->header( 'Access-Control-Allow-Origin', '*' );

// GET route for root
$app->get( '/', function(){
    echo json_encode( [
        'Hello' => 'Welcome to Kooben API'
    ] );
});


# Rutas para recipes
include $kooben->routes->recipes;


# Rutas para recipes
include $kooben->routes->ingredients;


# Rutas para sessions
include $kooben->routes->sessions;


# Rutas para accounts
include $kooben->routes->accounts;


# Rutas para supplies
include $kooben->routes->supplies;


# Rutas para measures
include $kooben->routes->measures;


# Rutas para supplies types
include $kooben->routes->suppliesTypes;


# Rutas para providers
include $kooben->routes->providers;


# Rutas para marks
include $kooben->routes->marks;


# Rutas para contries
include $kooben->routes->countries;


# Rutas para states
include $kooben->routes->states;


# Rutas para states
include $kooben->routes->cities;


# Rutas para states
include $kooben->routes->menus;


# Rutas para presentations
include $kooben->routes->presentations;


# Rutas para prices of supplies
include $kooben->routes->suppliesPrices;


# Rutas para consults of prices
include $kooben->routes->pricesConsults;


# Rutas para period consumption
include $kooben->routes->periodConsumption;


# Rutas para registry consumption
include $kooben->routes->registryConsumption;


# Rutas para registry consumption data
include $kooben->routes->registryConsumptionData;


# Rutas para shoping list
include $kooben->routes->shop;


# Rutas para user addresses
include $kooben->routes->addresses;


# Rutas para pleneacion
include $kooben->routes->purchases;


# Rutas para Shop Deliverers
include $kooben->routes->shopDeliverers;


# Rutas para planeaciones
include $kooben->routes->planeaciones;


# Run the Slim application
$app->run();

?>
