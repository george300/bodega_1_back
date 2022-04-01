<?php


use App\Http\Controllers\SimuladorController;
use Illuminate\Support\Facades\Route;
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
Route::resource('bodega','BodegaController');
Route::apiResource('menu','MenuController');
Route::get('/', function () {
    return view('welcome');
});

Route::get('listaMenu','MenuController@listaMenu');
Auth::routes(['register' => false]);
//usuarios salle


