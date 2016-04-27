<?php
/**
* API para Kooben
*
* @started 2015-10-10|21:00:00 hrs
* @author Martin Samuel Esteban Diaz <edmsamuel>
*/


# crear aplicación
include 'kooben.php';
$kooben = new Kooben();

# importar el nucleo
include $kooben->core->query;
include $kooben->core->model;

# importar Slim
require $kooben->core->slim;
\Slim\Slim::registerAutoloader();


# crear conexión a mysql
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


# crear instancia de Slim
$app = new \Slim\Slim();
$app->response()->header( 'Content-Type', 'application/json' );
$app->response()->header( 'Access-Control-Allow-Origin', '*' );

# ruta de entrada
$app->get( '/', function(){
    echo json_encode( [
        'Hello' => 'Welcome to Kooben API'
    ] );
});


# Rutas para recetas
include $kooben->routes->recipes;


# Rutas para ingredientes
include $kooben->routes->ingredients;


# Rutas para sesiones
include $kooben->routes->sessions;


# Rutas para cuentas
include $kooben->routes->accounts;


# Rutas para suministros
include $kooben->routes->supplies;


# Rutas para unidades
include $kooben->routes->measures;


# Rutas para tipos de suministros
include $kooben->routes->suppliesTypes;


# Rutas para proveedores
include $kooben->routes->providers;


# Rutas para marcas
include $kooben->routes->marks;


# Rutas para paises
include $kooben->routes->countries;


# Rutas para estados
include $kooben->routes->states;


# Rutas para ciudades/municipios
include $kooben->routes->cities;


# Rutas para menus
include $kooben->routes->menus;


# Rutas para presentaciones
include $kooben->routes->presentations;


# Rutas para precios de suministros
include $kooben->routes->suppliesPrices;


# Rutas para consulta de precios
include $kooben->routes->pricesConsults;


# Rutas para compras
include $kooben->routes->shop;


# Rutas para direcciones de entrega de usuarios
include $kooben->routes->addresses;


# Rutas para compras
include $kooben->routes->purchases;


# Rutas para repartidores
include $kooben->routes->shopDeliverers;


# Rutas para planeaciones
include $kooben->routes->planeaciones;


# Run the Slim application
$app->run();

?>
