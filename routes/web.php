<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/symlink', function () {
    $target = '/home/intranetimaarica/intranet/storage/app/public';
    $link = '/home/intranetimaarica/public_html/app/storage'; // Define un nombre específico para el symlink

    // Verificar si el enlace simbólico ya existe
    if (!file_exists($link)) {
        symlink($target, $link);
        echo "Enlace simbólico creado";
    } else {
        echo "El enlace simbólico ya existe";
    }
});

//13689472-2 password
