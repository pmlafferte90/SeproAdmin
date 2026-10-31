<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Empresa\ClienteController;
use App\Http\Controllers\Mantenimiento\AreaController;

Route::prefix('localidad')->group(function () {
    Route::get('/{id}', [ClienteController::class, 'getLocalidadById']);
    Route::get('/cliente/{id}', [ClienteController::class, 'getLocalidadByIdCliente']);
    Route::get('/subareas/{id_localidad}', [AreaController::class, 'getSubareasByLocalidad']);
    Route::post('/crear', [ClienteController::class, 'store_localidad_cliente']);
    Route::post('/actualizar/{id}', [ClienteController::class, 'update_localidad_cliente']);
    Route::post('/crear_subareas', [AreaController::class, 'asignarSubareasALocalidad']);

});



