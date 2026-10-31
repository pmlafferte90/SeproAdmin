<?php

use App\Http\Controllers\Empresa\ClienteController;
use App\Http\Controllers\Empresa\RepresentanteController;
use App\Http\Controllers\Mantenimiento\AreaController;
use App\Http\Controllers\Mantenimiento\ExtintorController;
use App\Http\Controllers\Mantenimiento\NomenclaturaController;
use App\Http\Controllers\Mantenimiento\UsuarioController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    if (auth()->check()) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('login');
    }
});

Route::get('/about', function () {
    return view('about');
});

/**
 * Auth Routes
 */
Auth::routes(['verify' => false]);


Route::group(['namespace' => 'App\Http\Controllers'], function()
{
    Route::middleware('auth')->group(function () {
        /**
         * Home Routes
         */
        Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
        /**
         * Role Routes
         */    
        Route::resource('roles', RolesController::class);
        /**
         * Permission Routes
         */    
        Route::resource('permissions', PermissionsController::class);
        /**
         * User Routes
         */
        Route::group(['prefix' => 'users'], function() {
            Route::get('/', [App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
            Route::get('/create', 'UsersController@create')->name('users.create');
            Route::post('/create', 'UsersController@store')->name('users.store');
            Route::get('/{user}/show', 'UsersController@show')->name('users.show');
            Route::get('/{user}/edit', 'UsersController@edit')->name('users.edit');
            Route::patch('/{user}/update', 'UsersController@update')->name('users.update');
            Route::delete('/{user}/delete', 'UsersController@destroy')->name('users.destroy');
            Route::post('/users/{user}/change-password', [UsersController::class, 'changePassword'])->name('users.changePassword');

        });

        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('representante', [RepresentanteController::class, 'index'])->name('representante.index');
        Route::get('usuario/check/username', [UsersController::class, 'checkUsername'])->name('usuario.checkUsername');
        Route::get('area', [AreaController::class, 'index'])->name('area.index');
        Route::get('usuario', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::get('subarea', [AreaController::class, 'get_sub_area'])->name('area.get_sub_area');
        Route::get('marca', [ExtintorController::class, 'get_marca'])->name('marca.get_marca');
        Route::get('peso', [ExtintorController::class, 'get_peso'])->name('peso.get_peso');
        Route::get('clase', [ExtintorController::class, 'get_clase'])->name('clase.get_clase');
        Route::get('control_extintor_cliente/{id_cliente}', [ExtintorController::class, 'obtener_control_extintor_por_cliente'])->name('obtener_control_extintor_por_cliente');
        Route::get('area_empresa/{id_cliente}', [AreaController::class, 'obtener_areas_empresa'])->name('area.area_empresa');
        Route::get('control_extintores', [ExtintorController::class, 'obtener_control_extintores'])->name('obtener_control_extintores');

        Route::get('nomenclatura', [NomenclaturaController::class, 'index'])->name('nomenclatura.index');
        Route::get('tipoNomenclatura', [NomenclaturaController::class, 'get_tipo_nomenclatura'])->name('tipoNomenclatura.get_tipo_nomenclatura');
        
        //RUTAS WEBS DE AREAS
        Route::group(['prefix' => 'area'], function() {
            Route::post('/crear', [AreaController::class, 'store'])->name('area.store');
            Route::post('/actualizar/{id}', [AreaController::class, 'update']);
            Route::put('/cambiarEstado/{id}', [AreaController::class, 'cambiarEstado']);
        });
        //RUTAS DE SUBAREA
        Route::group(['prefix' => 'subarea'], function() {
            Route::post('/crear', [AreaController::class, 'crear_subarea'])->name('subarea.crear_subarea');
            Route::post('/actualizar/{id}', [AreaController::class, 'actualizar_subarea']);
            Route::put('/cambiarEstado_subarea/{id}', [AreaController::class, 'cambiarEstado_subarea']);

        });
        //RUTAS DE AREA EMPRESA
        Route::group(['prefix' => 'area_empresa'], function() {
            Route::post('/crear', [AreaController::class, 'crear_area_empresa']);
            Route::put('/cambiarEstado_area_empresa/{id}', [AreaController::class, 'cambiarEstado_area_empresa']);
        });

        //RUTAS DE NOMENCLATURA
        Route::group(['prefix' => 'nomenclatura'], function() {
            Route::post('/crear', [NomenclaturaController::class, 'store']);
            Route::post('/actualizar/{id}', [NomenclaturaController::class, 'update']);
            Route::put('/cambiarEstado/{id}', [NomenclaturaController::class, 'cambiarEstado']);    
        });
        //RUTAS DE TIPO NOMENCLATURA
        Route::group(['prefix' => 'tipoNomenclatura'], function() {
            Route::post('/crear', [NomenclaturaController::class, 'crear_tipo_nomenclatura']);
            Route::put('/actualizar/{id}', [NomenclaturaController::class, 'actualizar_tipo_nomenclatura']);
            Route::put('/cambiarEstado/{id}', [NomenclaturaController::class, 'cambiarEstado_tipo_nomenclatura']);                
        });
        //RUTAS DE MATENIMIENTO USUARIOS
        Route::group(['prefix' => 'usuario'], function() {
            Route::post('/crear', [UsuarioController::class, 'crear_usuario'])->name('usuario.crear_usuario');
            Route::post('/actualizar/{id}', [UsuarioController::class, 'actualizar_usuario']);
            Route::put('/cambiarEstado_usuario/{id}', [UsuarioController::class, 'cambiarEstado_usuario']);

        });
        

    });
});

