<?php

namespace App\Http\Controllers;

use App\Models\CiUser;
use App\Models\Cliente;
use App\Models\ClienteLocalidad;
use App\Models\ControlExtintorCli;
use App\Models\Menu;
use App\Services\ExtintorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $extintorService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->extintorService = new ExtintorService();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $rol = auth()->user()->rol;
        $id_cliente = ($rol == 1 || $rol == 2) ? 0 : auth()->user()->id_cliente;
        Log::alert("id_cliente ".$id_cliente);

        // Log para verificar el rol del usuario
        Log::info('Rol del usuario: ' . $rol);
        // Obtener el rol del usuario autenticado
        $id_usuario = Auth::user()->id;
        Log::alert("id_usuario ".$id_usuario);


        // Obtener los menús basados en el rol del usuario
        $menus = $this->getMenusByRolUsuario($id_usuario);
        $usuariosActivos = CiUser::where('estado',1)->where('id','!=',1)->count();

        $clientesActivos = Cliente::where('estado', 1)->count(); // Cambia 'estado' según tu lógica

        $empresa = CiUser::select(
            "cliente.emp_origen",
            DB::raw("(CASE WHEN cliente.emp_origen = 1 THEN 'red' ELSE 'green' END) AS color")
            )
        ->join("cliente","cliente.id","=","ci_users.id_cliente")
        ->where("cliente.estado",1)
        ->where("ci_users.estado",1)
        ->where("ci_users.id",$id_usuario)
        ->first();

        $recarga = $this->extintorService->get_proxima_recarga_extintor($id_cliente, ['proxima_recarga_desde' => now()])->toArray();

        // $recarga = ControlExtintorCli::select(
        //     'cliente.razon_social AS empresa',
        //     'sub_area.abreviatura AS cod', 
        //     'sub_area.nombre AS ubicacion', 
        //     'clase_extintor.clase', 
        //     'marca_extintor.marca',  
        //     DB::raw("DATE_FORMAT(control_extintor_cli.fecha_prox_recarga, '%d/%m/%Y') AS fecha_prox_recarga")
        // )
        // ->join('cliente', 'cliente.id', '=', 'control_extintor_cli.id_empresa')
        // ->where('control_extintor_cli.estado', 1)
        // ->where('control_extintor_cli.fecha_prox_recarga', '>=', now()) // Filtrar fechas de hoy en adelante
        // ->orderBy('control_extintor_cli.fecha_prox_recarga', 'ASC') // Ordenar de más cercana a más lejana
        // ->get()
        // ->toArray();
     // Convierte la colección en un array
        

        $localidades = ClienteLocalidad::where('estado',1)->get();

        // Retornar la vista con los menús, el conteo de usuarios y clientes activos
        return view('home', [
            'menus' => $menus,
            "empresa" => $empresa,
            'usuariosActivos' => $usuariosActivos,
            'clientesActivos' => $clientesActivos,
            'recarga' => $recarga,
            'localidades' => $localidades
        ]);
        // return view('home');
    }

    public function about()
    {
        return view('about');
    }

    public function getMenusByRolUsuario($id_usuario)
    {
        // Obtener el id_rol del usuario desde la tabla ci_users
        $usuario =CiUser::select('rol')
        ->where('id', $id_usuario)
        ->first();

        // Verificar si el usuario tiene un rol asignado
        if (!$usuario || !$usuario->rol) {
            return []; // Devolver un array vacío si no tiene rol
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

            // Solo agregamos el menú si tiene submenús
            if ($submenus->isNotEmpty()) {
                $result[$menu->nombre] = [
                    'submenu' => $submenus->toArray()
                ];
            }
        }
        // Log::alert(collect($result));

        return $result;
    }

}
