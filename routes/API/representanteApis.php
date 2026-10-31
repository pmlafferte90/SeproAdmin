<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Empresa\RepresentanteController;

Route::prefix('representante')->group(function () {
    Route::post('/crear', [RepresentanteController::class, 'store'])->name('representante.store');
    Route::post('/actualizar/{id}', [RepresentanteController::class, 'update'])->name('representante.update');
    Route::get('/{id}', [RepresentanteController::class, 'getRepresentanteById'])->name('representante.get');  
});
