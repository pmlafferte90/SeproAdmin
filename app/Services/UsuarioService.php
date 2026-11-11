<?php

namespace App\Services;

use App\Models\CiUser; // Modelo de la tabla 'ci_users'
use Illuminate\Support\Facades\Hash; // Para la encriptación de la contraseña
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            $perPage = $request->input('perPage', 5);

            $query = CiUser::select(
                    'ci_users.id',
                    'ci_users.nombre',
                    'ci_users.apellido',
                    'ci_users.email',
                    'ci_users.usuario',
                    'cliente.razon_social',
                    'ci_users.password',
                    'ci_users.fecha_creacion',
                    'ci_users.fecha_fin',
                    'ci_users.estado'
                )
                ->leftJoin('cliente', 'cliente.id', '=', 'ci_users.id_cliente')
                ->where('ci_users.id', '!=', 1)
                ->with('roles');

            if ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('ci_users.usuario', 'like', "%{$buscar}%")
                        ->orWhere('ci_users.nombre', 'like', "%{$buscar}%")
                        ->orWhere('ci_users.apellido', 'like', "%{$buscar}%")
                        ->orWhere('ci_users.email', 'like', "%{$buscar}%")
                        ->orWhere('ci_users.fecha_creacion', 'like', "%{$buscar}%")
                        ->orWhere('cliente.razon_social', 'like', "%{$buscar}%")
                        ->orWhereHas('roles', function ($r) use ($buscar) {
                            $r->where('name', 'like', "%{$buscar}%");
                        });
                });
            }

            $usuarios = $query->orderBy('ci_users.id', 'desc')
                ->paginate($perPage)
                ->appends([
                    'buscar' => $buscar,
                    'perPage' => $perPage,
                ]);

            // Formatear para agregar el primer rol como campo directo (para facilitar uso en tabla o edición)
            $usuarios->getCollection()->transform(function ($usuario) {
                $usuario->rol = $usuario->roles->first()?->name ?? null;
                $usuario->id_rol = $usuario->roles->first()?->id ?? null;
                return $usuario;
            });

            return $usuarios;
        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios: ' . $e->getMessage());
            throw $e;
        }
     }

     public function crearUsuario(Request $request)
     {
        $request->validate([
            'usuario'    => 'required|string|max:25|unique:ci_users,usuario',
            'password'   => 'required|string|min:8|max:255',
            'rol_id'        => 'required|exists:roles,id',
            'nombre'     => 'required|string|max:100',
            'apellido'   => 'required|string|max:100',
            'email'      => 'required|email|max:150',
        ], [
            'email.required' => 'Por favor, ingresa un correo electrónico.',
            'email.email'    => 'El correo electrónico no es válido.',
            'email.unique'   => 'Este correo electrónico ya está registrado.',
            'usuario.required' => 'Por favor, ingresa un nombre de usuario.',
            'usuario.unique' => 'Este nombre de usuario ya está en uso.',
        ]);

        $id_usuario = auth()->id() ?? 1;

        $usuario = CiUser::create([
            'id_cliente'     => $request->input('id_cliente') ?? null,
            'usuario'        => $request->input('usuario'),
            'password'       => Hash::make($request->input('password')),
            'rol'            => $request->input('rol_id'),
            'nombre'         => $request->input('nombre'),
            'apellido'       => $request->input('apellido'),
            'email'          => $request->input('email'),
            'estado'         => 1,
            'fecha_creacion' => now(),
            'id_usuario'     => $id_usuario,
        ]);

        return $usuario;
     }

    public function editarUsuario($id, $request)
    {
        $usuario = CiUser::findOrFail($id);

        // Validar el request
        $validator = Validator::make($request->all(), [
            //'usuario'   => ['required', 'string', 'max:25', Rule::unique('ci_users')->ignore($id)],
            'email'     => ['required', 'email', 'max:150'],
            'nombre'    => ['required', 'string', 'max:100'],
            'apellido'  => ['required', 'string', 'max:100'],
            'rol_id'    => ['required', 'exists:roles,id'],
            'password'  => ['nullable', 'string', 'min:8']
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Preparar datos
        $datos = [
            'rol'       => $request->input('rol_id'),
            'nombre'    => $request->input('nombre'),
            'apellido'  => $request->input('apellido'),
            'email'     => $request->input('email'),
        ];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->input('password'));
        }

        $usuario->update($datos);

        return $usuario->fresh();
    }

    public function getBaseUsername(string $nombre, string $apellido): string
    {
        // Convertir a minúsculas y eliminar espacios
        $baseUsername = strtolower(preg_replace('/\s+/', '', mb_substr($nombre, 0, 1) . '.' . $apellido));
        return $baseUsername;
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

    public function cambiar_estado_usuario($id, $estado)
    {
        $usuario = CiUser::find($id);

        if (!$usuario) {
            throw new \Exception('Usuario no encontrado');
        }

        $usuario->estado = $estado; // Por defecto inactivo
        $usuario->fecha_fin = $usuario->estado == 0 ? now() : null; // Si se inactiva, establecer fecha fin

        $usuario->save();

        return $usuario;
    }

}
