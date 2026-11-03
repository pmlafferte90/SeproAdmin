<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\EmailService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(private EmailService $emailService, UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }
    public function index(Request $request)
    {
        try {

            $usuarios = $this->usuarioService->obtenerUsuarios($request);
            $roles = Role::all();

            return view('Submenus.usuarios', compact('usuarios', 'roles'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function cambiarEstado_usuario($id, Request $request)
    {
        try{
            $usuario = $this->usuarioService->cambiar_estado_usuario($id, $request->input('estado', 0));

            $mensaje = $usuario->estado == 1 ? 'El usuario ha sido restaurado exitosamente.' : 'El usuario ha sido inactivado exitosamente.';
            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }

    public function crear_usuario(Request $request)
    {
        DB::beginTransaction();

        try {
            // Crear el usuario
            $usuario = $this->usuarioService->crearUsuario($request);

            // Asignar rol (Spatie)
            if ($request->filled('rol_id')) {
                $rol = Role::findById($request->input('rol_id'));
                $usuario->syncRoles([$rol->name]);
            }

            // Enviar correo con credenciales
            $this->emailService->sendEmail(
                [$usuario->email],
                'Tus credenciales de acceso a Sepro',
                'emails.credenciales',
                [
                    'nombre'  => $usuario->nombre,
                    'usuario' => $usuario->usuario,
                    'password'=> $request->input('password'),
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente.',
                'data'    => $usuario
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);

        } catch (TransportExceptionInterface $e) {
            DB::rollBack();
            // Log::error('Error al enviar correo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'No se pudo enviar el correo con las credenciales. Verifica la conexión o configuración del correo.',
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error inesperado al crear usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => 'Error inesperado al crear el usuario.',
                'details' => $e->getMessage(), // puedes quitar esto en producción
            ], 500);
        }
    }

    public function actualizar_usuario(Request $request, $id)
    {
        try {

            $usuarioActualizado = $this->usuarioService->editarUsuario($id, $request);

            // ✅ Asignar nuevo rol si fue enviado
            if ($request->filled('rol_id')) {
                $rol = Role::findById($request->input('rol_id'));
                $usuarioActualizado->syncRoles([$rol->name]);
            }

            if ($request->filled('password')) {
                $this->emailService->sendEmail(
                    [$usuarioActualizado->email],
                    'Tu nueva contraseña de acceso a Sepro',
                    'emails.credenciales',
                    [
                        'nombre' => $usuarioActualizado->nombre,
                        'usuario' => $usuarioActualizado->usuario,
                        'password' => $request->input('password'),
                    ]
                );
            }

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente.',
                'data'    => $usuarioActualizado
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

    public function checkUsername(Request $request)
    {
        $baseUsername = $request->query('username');

        if (!$baseUsername) {
            return response()->json([
                'success' => false,
                'message' => 'Debe enviar el username base para validar.'
            ], 400);
        }

        $usernameUnico = $this->usuarioService->obtenerUsernameUnico($baseUsername);

        return response()->json([
            'success' => true,
            'username' => $usernameUnico
        ]);
    }

}
