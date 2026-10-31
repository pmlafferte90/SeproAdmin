<?php

namespace App\Services;

use App\Models\CiUser; // Modelo de la tabla 'ci_users'
use Illuminate\Support\Facades\Hash; // Para la encriptación de la contraseña
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UsuarioService
{
    /**
     * Crea un usuario en la tabla ci_users
     *
     * @param array $data
     * @return CiUser|null
     */

     public function obtenerUsuarios($request)
     {
         try {
             $buscar = $request->input('buscar');
              $perPage = $request->input('perPage', 5); // Valor predeterminado: 10
         
             $query = CiUser::select(
                'ci_users.id',
                'ci_users.usuario',
                'cliente.razon_social',
                'ci_rol.rol',
                'ci_users.password',
                'ci_rol.id AS id_rol',
                'ci_users.fecha_creacion',
                'ci_users.fecha_fin',
                'ci_users.estado'
             )
             ->join('ci_rol','ci_rol.id','=','ci_users.rol')
             ->leftjoin('cliente','cliente.id','=','ci_users.id_cliente')
             ->where('ci_users.id','!=',1); // Usar Query Builder en lugar de all()
 
             if ($request->has('buscar')) {
                 $buscar = $request->input('buscar');
                 $query->where('ci_users.usuario', 'like', "%{$buscar}%")
                     ->orWhere('ci_rol.rol', 'like', "%{$buscar}%")
                     ->orWhere('ci_users.fecha_creacion', 'like', "%{$buscar}%")
                     ->orWhere('cliente.razon_social', 'like', "%{$buscar}%");
             }
 
            //  $clientes = $query->get(); 
             $clientes = $query->paginate($perPage)->appends([
                 'buscar' => $buscar,
                 'perPage' => $perPage,
             ]);
             return $clientes;
         } catch (\Exception $e) {
             Log::error('Error al obtener clientes: ' . $e->getMessage());
             throw $e;
         }
     }

     public function crearUsuario(Request $request)
     {
         try {
             // Validar datos del usuario
             $request->validate([
                 'id_cliente' => 'required|exists:cliente,id',
                 'usuario' => 'required|string|max:25|unique:ci_users,usuario',
                 'identificacion' => 'required|string|max:13|unique:representante,identificacion',
             ]);
     
             // Obtener ID del usuario autenticado
             $id_usuario = auth()->id() ?? 1;
     
             // Crear el usuario
             $usuario = CiUser::create([
                 'id_cliente' => $request->input('id_cliente'),
                 'usuario' => $request->input('usuario'),
                 'password' => Hash::make($request->input('password')), // Encriptar la contraseña
                 'rol' => $request->input('rol'), // Representante o cliente
                 'estado' => 1,
                 'fecha_creacion' => now(),
                 'id_usuario' => $id_usuario,
             ]);
     
             return response()->json([
                 'ok' => true,
                 'message' => 'Usuario creado con éxito',
                 'data' => $usuario
             ], 201);
         } catch (\Exception $e) {
             return response()->json([
                 'ok' => false,
                 'error' => 'No se pudo crear el usuario'
             ], 500);
         }
     }

    public function editarUsuario($id, $request)
    {
        try {
            $usuario = CiUser::findOrFail($id);

            $usuario->update([
                'usuario' => $request->input('usuario') ?? $usuario->usuario,
                'rol' => $request->input('rol_id') ?? $usuario->rol,
                'password' => ($request->input('password') && $request->input('password') != $usuario->password) 
                                ? Hash::make($request->input('password')) 
                                : $usuario->password
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerUsernameUnico(string $baseUsername): string
    {
        $username = $baseUsername;
        $contador = 1;

        while (CiUser::where('usuario', $username)->where('estado', 1)->exists()) {
            $username = $baseUsername . $contador;
            $contador++;
        }

        return $username;
    }

}
