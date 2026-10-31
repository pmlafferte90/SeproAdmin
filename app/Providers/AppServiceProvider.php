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
        // Usar un View Composer para todas las vistas que necesiten los menús
        View::composer('*', function ($view) {
            $user = auth()->user(); // Obtener el usuario autenticado

            // Si no hay usuario autenticado, devolver un array vacío
            if (!$user) {
                $view->with('menus', []);
                return;
            }

            // Obtener el rol del usuario desde la tabla ci_users
            $usuario = CiUser::select('rol')->where('id', $user->id)->first();

            // Si no tiene un rol asignado, devolver un array vacío
            if (!$usuario || !$usuario->rol) {
                $view->with('menus', []);
                return;
            }

            $id_rol = $usuario->rol;
            $menus = Menu::where('estado', 1)->get();
            $result = [];

            foreach ($menus as $menu) {
                $submenus = $menu->submenus()
                    ->whereHas('roles', function ($query) use ($id_rol) {
                        $query->where('rol_submenu.id_rol', $id_rol)
                              ->where('rol_submenu.estado', 1);
                    })
                    ->where('estado', 1)
                    ->get(['nombre', 'ruta']);

                // Agregar el menú si tiene submenús
                if ($submenus->isNotEmpty()) {
                    $result[$menu->nombre] = [
                        'submenu' => $submenus->toArray(),
                    ];
                }
            }

            // Registrar en los logs para depuración (opcional)
            // Log::alert(collect($result));

            // Pasar los menús a la vista
            $view->with('menus', $result);
        });

    }
}
