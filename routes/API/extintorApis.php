<?php

use App\Http\Controllers\Mantenimiento\ExtintorController;
use Illuminate\Support\Facades\Route;

Route::post('/marca/crear', [ExtintorController::class, 'insertar_marca']);
Route::put('/marca/cambiarEstado_marca/{id}', [ExtintorController::class, 'cambiarEstado_marca']);
Route::post('/peso/crear', [ExtintorController::class, 'insertar_peso']);
Route::put('/peso/cambiarEstado_peso/{id}', [ExtintorController::class, 'cambiarEstado_peso']);
Route::post('/clase/crear', [ExtintorController::class, 'insertar_clase']);
Route::put('/clase/cambiarEstado_clase/{id}', [ExtintorController::class, 'cambiarEstado_clase']);
Route::post('/controlExtintor/crear', [ExtintorController::class, 'insertar_control_extintor']);
Route::get('/control-extintores/export', [ExtintorController::class, 'export'])->name('control_extintores.export');
Route::post('control-extintores/{id}/actualizar', [ExtintorController::class, 'update_control_extintor'])->name('actualizar_control_extintores');
Route::put('/control-extintores/cambiarEstado_control_extintores/{id}', [ExtintorController::class, 'cambiarEstado_control_extintores'])->name('cambiarEstado_control_extintores');
Route::post('/control-extintores/enviar-reporte', [ExtintorController::class, 'sendReportEmail']);
Route::get('/control_extintor_cliente/{cliente}/ubicaciones',[ExtintorController::class, 'ubicacionesPorRevisar'])->name('control_extintor.ubicaciones');
Route::get('extintorApis/{cliente}/detalle', [ExtintorController::class, 'detalleExtintorExcel'])->name('extintor.detalleExtintorExcel');
Route::get('/reporte-resumen-extintores', [ExtintorController::class, 'exportResumenExtintores'])->name('reporte_resumen_extintores');
Route::get('/control-extintor/ultimo', [ExtintorController::class, 'getUltimoControl']);




