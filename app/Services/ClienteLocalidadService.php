<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\ClienteLocalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClienteLocalidadService
{
    public function crearLocalidad(array $data) //Recibe un array de datos de una localidad
    {
        try {
            $id_usuario = auth()->user()->id ?? 1;

            $localidad = ClienteLocalidad::create([
                'id_cliente' => $data['id_cliente'],
                'nombre' => $data['nombre_localidad'],
                'direccion' => $data['direccion_localidad'],
                'ubicacion' => $data['ubicacion_localidad'],
                'id_usuario' => $id_usuario,
                'estado' => 1,
                'fecha_creacion' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Localidad registrada con éxito',
                'data' => $localidad
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear localidad: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' => 'No se pudo registrar la localidad'
            ], 500);
        }
    }

    public function crearLocalidades($id_cliente, $localidades) //Recibe un array de localidades
    {
        try {
            $id_usuario = auth()->user()->id ?? 1;

            foreach ($localidades as $localidad) {
                ClienteLocalidad::create([
                    'id_cliente' => $id_cliente,
                    'nombre' => $localidad['nombrelocalidad'],
                    'direccion' => $localidad['direccion'],
                    'ubicacion' => $localidad['ubicacion'],
                    'id_usuario' => $id_usuario,
                    'estado' => 1,
                    'fecha_creacion' => now(),
                ]);
            }

            return response()->json([
                'ok' => true,
                'message' => 'Localidades registradas con éxito'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear localidades: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' => 'No se pudieron registrar las localidades'
            ], 500);
        }
    }

    public function actualizarLocalidad(Request $request, $id)
    {
        try {
            $id_usuario = auth()->user()->id ?? 1;

            $localidad = ClienteLocalidad::findOrFail($id);

            $request->validate([
                'id_cliente' => 'required|exists:cliente,id',
                'nombre_localidad' =>  [
                    'required',
                    'string',
                    'max:255',
                    'unique:cliente_localidad,nombre,' . $id . ',id', 
                ],
                'direccion_localidad' => 'required|string|max:255',
                'ubicacion_localidad' => 'required|url',
            ], [
                'nombre_localidad.unique' => 'El nombre de localidad ingresado ya está registrado.',
            ]);

            $localidad->update([
                'nombre' => $request->input('nombre_localidad') ?? $localidad->nombre,
                'direccion' => $request->input('direccion_localidad') ?? $localidad->direccion,
                'ubicacion' => $request->input('ubicacion_localidad') ?? $localidad->ubicacion,
                'estado' => $request->input('estado') ?? $localidad->estado,
                'fecha_actualizacion' => now(),
                'id_usuario_auditor' => $id_usuario
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Localidad actualizada con éxito',
                'data' => $localidad
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al actualizar localidad: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLocalidadById($id)
    {
        try {

            $cliente = ClienteLocalidad::where("id", $id)->first();

            return $cliente;
        } catch (\Exception $e) {
            Log::error('Error al obtener localidad by id: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getLocalidadByIdCliente($id_cliente)
    {
        try {

            $localidades = ClienteLocalidad::where("id_cliente", $id_cliente)
            ->where('estado',1)
            ->orderBy('nombre', 'asc')
            ->get();

            return response()->json([
                'ok' => true,
                'data' => $localidades
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al obtener localidad by id_cliente: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' =>  $e->getMessage()
            ], 500);
        }
    }
}
