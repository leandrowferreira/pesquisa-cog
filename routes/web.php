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

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('home');
});

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/home', 'HomeController@index')->name('home');

// Route::resource('/disciplinas', 'DisciplinaController')->only(['index', 'show']);
Route::get('/disciplinas', 'DisciplinaController@index');
Route::get('/disciplinas/{disciplina}/{professor}', 'DisciplinaController@show');

Route::get('/async/perguntas', 'PerguntaController@index');
Route::get('/async/user', 'UserController@show');
Route::patch('/async/user/{user}', 'UserController@update');
Route::post('/async/disciplinas/{disciplina}/{professor}', 'DisciplinaController@store');
