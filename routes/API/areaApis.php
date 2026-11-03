<?php

use App\Http\Controllers\Empresa\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mantenimiento\AreaController;

Route::post('/area/crear', [AreaController::class, 'store']);
Route::post('area/{id}/actualizar', [AreaController::class, 'update']);
Route::put('/area/cambiarEstado/{id}', [AreaController::class, 'cambiarEstado']);
Route::post('/subarea/crear', [AreaController::class, 'crear_subarea']);
Route::post('subarea/{id}/actualizar', [AreaController::class, 'actualizar_subarea']);
Route::put('/subarea/cambiarEstado_subarea/{id}', [AreaController::class, 'cambiarEstado_subarea']);
Route::post('/area_empresa/crear', [AreaController::class, 'crear_area_empresa']);
Route::put('/area_empresa/cambiarEstado_area_empresa/{id}', [AreaController::class, 'cambiarEstado_area_empresa']);
Route::get('/areas_empresa/existe/{id_localidad}', [AreaController::class, 'checkExistRelationAreaEmpresa']);
Route::get('/subareas_activas', [AreaController::class, 'subareas_activas_api']);


