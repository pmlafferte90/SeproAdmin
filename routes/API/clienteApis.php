<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Empresa\ClienteController;

Route::prefix('cliente')->group(function () {
    Route::post('/crear_cliente', [ClienteController::class, 'store'])->name('clientes.store');  // Crear cliente
    Route::post('/actualizar_cliente/{id}', [ClienteController::class, 'update'])->name('clientes.update');  // Actualizar cliente
    Route::get('/{id}', [ClienteController::class, 'getClienteById'])->name('clientes.get');  // Obtener cliente
    Route::put('/cambiar_estado/{id}', [ClienteController::class, 'cambiarEstado']);
});

