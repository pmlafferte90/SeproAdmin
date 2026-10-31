<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\ClienteLocalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClienteService
{
    public function getClienteById($id)
    {
        try {

            $cliente = Cliente::where("id", $id)->first();

            return $cliente;
        } catch (\Exception $e) {
            Log::error('Error al obtener cliente by id: ' . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerClientes($request)
    {
        try {
            $buscar = $request->input('buscar');
            $perPage = $request->input('perPage', 5);

            $query = Cliente::query()->where('estado', 1)->with('localidades');

            if ($request->filled('buscar')) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('razon_social', 'like', "%{$buscar}%")
                        ->orWhere('ruc', 'like', "%{$buscar}%")
                        ->orWhere('telefono', 'like', "%{$buscar}%");
                });
            }

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

    public function obtenerClientesActivos($id_cliente = 0)
    {
        try {

            $clientes = Cliente::where('estado', 1);
            if($id_cliente != 0){
                $clientes = $clientes->where('id',$id_cliente);
            }
            $clientes = $clientes->get();

            return $clientes;

        } catch (\Exception $e) {
           Log::error('Error al obtener clientes activos: ' . $e->getMessage());
           return $e->getMessage();
        }
    }

    public function crearCliente(Request $request)
    {
        try {
            $id_usuario = auth()->user()->id ?? 1;
            // Validar los datos del request
            $request->validate([
                'razon_social' => 'required|string|max:255',
                'ruc' => 'required|numeric|digits_between:10,13|unique:cliente',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'nullable|email|max:255',
                'id_empresa' => 'required|exists:empresa_origen,id',
            ], [
                'ruc.digits_between' => 'El RUC debe tener entre 10 y 13 dígitos.',
                'ruc.unique' => 'El RUC ingresado ya está registrado.',
                'correo.email' => 'El Correo debe ser una dirección de correo válida.',
                'id_empresa.required' => 'Debe seleccionar una Empresa.',
                'id_empresa.exists' => 'La Empresa seleccionada no existe en nuestros registros.',
            ]);

            // Crear el cliente y almacenar en una variable
            $cliente = Cliente::create([
                'razon_social' => $request->input('razon_social'),
                'ruc' => $request->input('ruc'),
                'telefono' => $request->input('telefono'),
                'correo' => $request->input('correo'),
                'id_empresa' => $request->input('id_empresa'),
                'estado' => 1,
                'fecha_creacion' => now(),
                'id_usuario' => $id_usuario
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Cliente creado con éxito',
                'data' => $cliente
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear cliente: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' =>  $e->getMessage()
            ], 500);
        }
    }

    public function actualizarCliente(Request $request, $id)
    {
        try {
            $id_usuario = auth()->user()->id ?? 1;

            $cliente = Cliente::findOrFail($id);

            $request->validate([
                'razon_social' => 'required|string|max:255',
                'ruc' =>  [
                    'string',
                    'max:13',
                    'unique:cliente,ruc,' . $id . ',id', // Usamos el ID del representante
                ],
                'telefono' => 'string|max:20',
                'correo' => 'email|max:255',
                'id_empresa' => 'required|exists:empresa_origen,id',
            ], [
                'ruc.digits_between' => 'El RUC debe tener entre 10 y 13 dígitos.',
                'ruc.unique' => 'El RUC ingresado ya está registrado.',
                'correo.email' => 'El Correo debe ser una dirección de correo válida.',
                'id_empresa.required' => 'Debe seleccionar una Empresa.',
                'id_empresa.exists' => 'La Empresa seleccionada no existe en nuestros registros.',
            ]);


            $cliente->update([
                'razon_social' => $request->input('razon_social') ?? $cliente->razon_social,
                'ruc' => $request->input('ruc') ?? $cliente->ruc,
                'telefono' => $request->input('telefono') ?? $cliente->telefono,
                'correo' => $request->input('correo') ?? $cliente->correo,
                'emp_origen' => $request->input('id_empresa') ?? $cliente->emp_origen,
                'estado' => $request->input('estado') ?? $cliente->estado,
                'fecha_creacion' => $request->input('fecha_creacion') ?? $cliente->fecha_creacion,
                'fecha_actualizacion' => now(),  // Asigna la fecha actual directamente
                'id_usuario_auditor' => $id_usuario,  // Asigna el id del usuario auditor
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Cliente actualizado con éxito',
                'data' => $cliente
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al actualizar cliente: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cambiarEstadoCliente($id, $estado)
    {
        try {
            $cliente = Cliente::findOrFail($id);

            $cliente->estado = $estado; // Actualizar el estado
            if ($estado == 0) {
                $cliente->fecha_fin = now();
            }
            $cliente->save(); // Guardar los cambios

            return $cliente;
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estado del cliente: ' . $e->getMessage());
            throw $e;
        }
    }

}
