<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
require __DIR__.'/API/clienteApis.php';
require __DIR__.'/API/representanteApis.php';
require __DIR__.'/API/areaApis.php';
require __DIR__.'/API/nomenclaturaApis.php';
require __DIR__.'/API/extintorApis.php';
require __DIR__.'/API/localidadApis.php';


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

