<?php

use App\Http\Controllers\Mantenimiento\NomenclaturaController;
use Illuminate\Support\Facades\Route;

Route::post('/nomenclatura/crear', [NomenclaturaController::class, 'store']);
Route::post('nomenclatura/{id}/actualizar', [NomenclaturaController::class, 'update']);
Route::put('/nomenclatura/cambiarEstado/{id}', [NomenclaturaController::class, 'cambiarEstado']);
Route::post('/tipoNomenclatura/crear', [NomenclaturaController::class, 'crear_tipo_nomenclatura']);
Route::put('tipoNomenclatura/{id}/actualizar', [NomenclaturaController::class, 'actualizar_tipo_nomenclatura']);
Route::put('/tipoNomenclatura/cambiarEstado/{id}', [NomenclaturaController::class, 'cambiarEstado_tipo_nomenclatura']);