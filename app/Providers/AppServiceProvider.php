<?php

namespace App\Providers;

use App\Models\CiUser;
use App\Models\Menu;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            if (! $user) {
                return $view->with('menus', []);
            }

            // Obtenemos directamente el rol
            $id_rol = CiUser::where('id', $user->id)
                            ->value('rol');

            if (! $id_rol) {
                return $view->with('menus', []);
            }

            // 1) Cargamos TODOS los menús activos
            // 2) Eager-load de submenus con el filtro según el rol
            $menus = Menu::where('estado', 1)
                ->with(['submenus' => function ($q) use ($id_rol) {
                    // Sólo submenús activos
                    $q->where('estado', 1);

                    // Si no es admin, filtramos por rol en el pivot
                    if ($id_rol !== 1) {
                        $q->whereHas('roles', function ($qr) use ($id_rol) {
                            $qr->where('rol_submenu.id_rol',    $id_rol)
                               ->where('rol_submenu.estado',   1);
                        });
                    }
                }])
                ->get();

            // Armamos el array final sin volver a tocar la BD
            $result = [];
            foreach ($menus as $menu) {
                if ($menu->submenus->isNotEmpty()) {
                    $result[$menu->nombre] = [
                        'submenu' => $menu->submenus
                            ->map(fn($s) => [
                                'nombre' => $s->nombre,
                                'ruta'   => $s->ruta,
                            ])
                            ->toArray(),
                    ];
                }
            }

            $view->with('menus', $result);
        });
    }
}
