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


/*
|--------------------------------------------------------------------------
| MySQL
|--------------------------------------------------------------------------
|
*/
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


/*
|--------------------------------------------------------------------------
| Application Models Required
|--------------------------------------------------------------------------
|
| Las siguientes instancias de la clase Model no deben borrarse
| ya que estás son implementadas en funciones genericas
| descritas en `core/generics.php`.
|
*/


# kardex
$kardex = new Model( 'kardex', $mysql );
$kardex->setProperties( $kooben->models->kardex );

# sessiones
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


/*
|--------------------------------------------------------------------------
| Application Models
|--------------------------------------------------------------------------
|
| A partir de aquí se deben de incluir los modelos que se utilizarán
| en las rutas.
|
*/

# geolocalización
include 'models/geolocalizacion.php';

# proveedores
include 'models/proveedores.php';

# tienda
include 'models/shop.php';

# planeaciones
include 'models/planeaciones.php';

# productos
include 'models/productos.php';

# recetas
include 'models/recetas.php';


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| A partir de aquí se deben de incluir las rutas que se desean que
| Slim sobreescriba.
|
*/

# principal
$app->get( '/', function(){
    echo json_encode( [
        'Hello' => 'Welcome to Kooben API'
    ] );
});

# recetas
include $kooben->routes->recipes;


# ingredientes
include $kooben->routes->ingredients;


# sesiones
include $kooben->routes->sessions;


# cuentas
include $kooben->routes->accounts;


# suministros
include $kooben->routes->supplies;


# unidades
include $kooben->routes->measures;


# tipos de suministros
include $kooben->routes->suppliesTypes;


# proveedores
include $kooben->routes->providers;


# marcas
include $kooben->routes->marks;


# paises
include $kooben->routes->countries;


# estados
include $kooben->routes->states;


# ciudades/municipios
include $kooben->routes->cities;


# menus
include $kooben->routes->menus;


# presentaciones
include $kooben->routes->presentations;


# precios de suministros
include $kooben->routes->suppliesPrices;


# consulta de precios
include $kooben->routes->pricesConsults;


# compras
include $kooben->routes->shop;


# direcciones de entrega de usuarios
include $kooben->routes->addresses;


# compras
include $kooben->routes->purchases;


# repartidores
include $kooben->routes->shopDeliverers;


# planeaciones
include $kooben->routes->planeaciones;


# geolocalización
include $kooben->routes->geolocation;


# Run the Slim application
$app->run();

?>
