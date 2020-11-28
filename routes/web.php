<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Cargando clasess
use App\Http\Middleware\ApiAuthMiddleware;

//RUTAS DE PRUEBA
Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba/{nombre?}', function($nombre = null) {
	$texto = '<h2>Texto de la ruta</h2>' ;
	$texto .= 'nombre =' .$nombre;

	return view ('pruebas', array(
		'texto' => $texto
	));
});


Route::get('/animales', 'PruebasController@index');
Route::get('/testOrm', 'PruebasController@testOrm');


//RUTAS DE API

/*Metodos http comunes
* GET : Consigue datos y recursos
* POST : Guardar datos y recursos o hacer lógica
* PUT : Actualiza recursos y datos
* DELETE : Elimina datos o recursos
*/

	
	//Rutas de prueba
	Route::get('/usuario/pruebas', 'UserController@pruebas');
	Route::get('/categoria/pruebas', 'CategoryController@pruebas');
	Route::get('/entrada/pruebas', 'PostController@pruebas');

	//Rutas del controlador de usuarios
	Route::post('/api/register', 'UserController@register');
	Route::post('/api/login', 'UserController@login');
	Route::put('/api/user/update', 'UserController@update');
	Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
	Route::get('/api/user/avatar/{filename}', 'UserController@getimage');