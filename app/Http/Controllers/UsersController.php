<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CiUser;
use App\Services\UsuarioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 *
 */
class UsersController extends Controller
{
    /**
     * Display all users
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show form for creating user
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user
     *
     * @param User $user
     * @param StoreUserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(User $user, StoreUserRequest $request)
    {
        //For demo purposes only. When creating user or inviting a user
        // you should create a generated random password and email it to the user
        $user->create(array_merge($request->validated(), [
            'password' => 'test'
        ]));

        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /**
     * Show user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(CiUser $user)
    {
        $userData = CiUser::select(
                "ci_users.id",
                "ci_users.usuario",
                "cliente.razon_social AS empresa",
                "ci_rol.rol",
                "ci_users.fecha_creacion" // Asegúrate de seleccionar la fecha de creación
            )
            ->leftJoin("cliente", "cliente.id", "=", "ci_users.id_cliente")
            ->join("ci_rol", "ci_rol.id", "=", "ci_users.rol")
            ->where('ci_users.id', $user->id)
            ->firstOrFail();
            $userData->fecha_creacion = $userData->fecha_creacion ? Carbon::parse($userData->fecha_creacion) : null;

        return view('users.show', [
            'user' => $userData
        ]);
    }

    public function changePassword(Request $request, $id)
    {
        try {
            // Validar la entrada
            $validatedData = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ], [
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]);
    
            // Obtener el usuario
            $user = CiUser::findOrFail($id);
    
            // Verificar que el usuario autenticado sea el mismo que el usuario objetivo
            if (auth()->user()->id !== $user->id) {
                abort(403, 'No tienes permiso para cambiar la contraseña de este usuario.');
            }
    
            // Actualizar la contraseña
            $user->password = bcrypt($validatedData['password']);
            $user->save();
    
            // Configurar el mensaje de éxito
            return redirect()->route('users.show', $user->id)->with('success', 'Contraseña actualizada con éxito.');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si ocurre una excepción de validación, redirigir de nuevo con los errores
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Captura cualquier otra excepción
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error inesperado.'])->withInput();
        }
    }
    
    



    /**
     * Edit user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }

    /**
     * Update user data
     *
     * @param User $user
     * @param ProfileUpdateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, ProfileUpdateRequest $request)
    {
        $user->update($request->validated());

        $user->syncRoles($request->get('role'));

        return redirect()->route('users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    /**
     * Delete user data
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
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

        $usernameUnico = app(UsuarioService::class)->obtenerUsernameUnico($baseUsername);

        return response()->json([
            'success' => true,
            'username' => $usernameUnico
        ]);
    }

}
