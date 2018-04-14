<?php

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');

Route::post("/newClient", "ClientsController@newClient")->middleware('auth');
Route::view("/newClient", "newClient")->middleware('auth');

Route::delete("/client", "ClientsController@deleteClient")->middleware('auth');
Route::get("/client/{websiteId}", "ClientsController@viewClient")->middleware('auth');
Route::get("/clients", "ClientsController@getClients")->middleware('auth');

Route::get("/deleteClient/{websiteId}", "ClientsController@confirmDeleteClient")->middleware('auth');

Route::get("/users", "SupportsController@users")->middleware('auth');