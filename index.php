<?php
/**
* API for Kooben
*
* Started at 2015-10-10 - 21:00:00 hrs
* Created by Martin Samuel Esteban Diaz
*
* Load the Slim Micro-Framework
* for use in the project
*/


/* Get the general configuration */
$kooben = json_decode( file_get_contents( 'config.json' ) );

/* Import the core */
include $kooben->core->query;
include $kooben->core->model;

require 'Slim/Slim.php';
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


# Routes for recipes #
include $kooben->routes->recipes;


# Routes for recipes #
include $kooben->routes->ingredients;


# Routes for sessions #
include $kooben->routes->sessions;


# Routes for accounts #
include $kooben->routes->accounts;


# Routes for supplies #
include $kooben->routes->supplies;


# Routes for measures #
include $kooben->routes->measures;


# Routes for supplies types #
include $kooben->routes->suppliesTypes;


# Routes for providers #
include $kooben->routes->providers;


# Routes for marks #
include $kooben->routes->marks;


# Routes for contries #
include $kooben->routes->countries;


# Routes for states #
include $kooben->routes->states;


# Routes for states #
include $kooben->routes->cities;


# Routes for states #
include $kooben->routes->menus;


# Routes for presentations #
include $kooben->routes->presentations;


# Routes for prices of supplies #
include $kooben->routes->suppliesPrices;


# Routes for consults of prices #
include $kooben->routes->pricesConsults;


# Routes for period consumption #
include $kooben->routes->periodConsumption;


# Routes for registry consumption #
include $kooben->routes->registryConsumption;


# Routes for registry consumption data #
include $kooben->routes->registryConsumptionData;


# Routes for shoping list #
include $kooben->routes->shop;


# Routes for user addresses #
include $kooben->routes->addresses;

include $kooben->routes->purchases;

#include $kooben->routes->purchasesItems;

include $kooben->routes->shopDeliverers;


# Run the Slim application
$app->run();

?>
