<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\CiUser;
use App\Models\Rol;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }
    public function index(Request $request)
    {
        try {

            $usuarios = $this->usuarioService->obtenerUsuarios($request);
            $roles = Rol::all();


            return view('Submenus.usuarios', compact('usuarios', 'roles'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function cambiarEstado_usuario($id, Request $request)
    {
        try{
            $usuario = CiUser::find($id);

            if ($usuario) {
                $usuario->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                if($request->input('estado') == 0) {
                    $usuario->fecha_fin = now();
                }
                $usuario->save(); // Guardar los cambios

                $mensaje = $usuario->estado == 1 ? 'El usuario ha sido restaurado exitosamente.' : 'El usuario ha sido inactivado exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }

    public function crear_usuario(Request $request)
    {
        // Validar y procesar datos
        try {
            // // Validar los datos del request
            // $request->validate([
            //     'id_cliente' => 'integer', // Aseguramos que el área exista
            //     'usuario' => 'required|string', // Aseguramos que el área exista
            //     'password' => 'required|string|max:255',
            //     'rol' => 'integer'
            // ]);

            $rol = auth()->user()->rol;

            $this->usuarioService->crearUsuario([
                'id_cliente' => 0,
                'usuario' => $request->input('usuario'),
                'password' => $request->input('password'),
                'rol' => $request->input('rol_id'),
                'estado' => (($rol == 1 || $rol == 2  ) ? 1 : 2),
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizar_usuario(Request $request, $id)
    {
        try {
            $request->validate([
                // 'usuario' => 'required|string|max:255',
                'password' => 'string|max:255',
                    'rol_id' => 'required|integer'
                ], [
                    'usuario.unique' => 'El usuario ya se encuentra registrado.',
                    'rol.required' => 'El rol es obligatorio.',
                ]);

            // Llamar al servicio para actualizar un área
            $this->usuarioService->editarUsuario($id, $request);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente.',
            ]);
        }catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422); 
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
