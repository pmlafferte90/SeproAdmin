<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Representante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RepresentanteService
{
    public function getRepresentanteById($id)
    {
        try {

            $representante = Representante::with('localidades')->find($id);

            return $representante;
        } catch (\Exception $e) {
            Log::error('Error al obtener representante by id: ' . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerRepresentantes($request)
    {
        try {
            $buscar = $request->input('buscar');
            $perPage = $request->input('perPage', 5);

            $query = Representante::select(
                'representante.id',
                'representante.nombre',
                'representante.apellido',
                'representante.identificacion',
                'representante.telefono',
                'representante.correo',
                'representante.estado',
                'representante.fecha_creacion',
                'representante.fecha_actualizacion',
                'representante.ver_todas_localidades',
                'cliente.razon_social'
            )
            ->join('cliente','cliente.id','=','representante.id_cliente')
            ->orderByDesc('representante.id');
    
            if ($request->filled('buscar')) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('cliente.razon_social', 'like', "%{$buscar}%")
                    ->orWhere('representante.identificacion', 'like', "%{$buscar}%")
                    ->orWhere('representante.telefono', 'like', "%{$buscar}%")
                    ->orWhere('representante.nombre', 'like', "%{$buscar}%")
                    ->orWhere('representante.apellido', 'like', "%{$buscar}%")
                    ->orWhereHas('localidades', function ($q2) use ($buscar) {
                        $q2->where('nombre', 'like', "%{$buscar}%");
                    });
                });
            }

            $representantes = $query->with('localidades')->paginate($perPage)->appends([
                'buscar' => $buscar,
                'perPage' => $perPage,
            ]);

            return $representantes;
        } catch (\Exception $e) {
            Log::error('Error al obtener representantes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function crearRepresentante(Request $request, $id_user)
    {
        try {
            // Validar los datos del representante
            $request->validate([
                'id_cliente' => 'required|exists:cliente,id',
                'id_localidad' => 'required|array|min:1',
                'id_localidad.*' => 'exists:cliente_localidad,id',
                'nombre' => 'required|string|max:50',
                'apellido' => 'required|string|max:50',
                'identificacion' => 'required|string|max:13|unique:representante,identificacion',
                'telefono' => 'nullable|string|max:15',
                'correo' => 'nullable|email|max:255',
                'ver_todas_localidades' => 'nullable|boolean',
            ]);
    
            // Obtener ID del usuario autenticado
            $id_usuario = auth()->id() ?? 1;
    
            // Crear el representante
            $representante = Representante::create([
                'id_cliente' => $request->input('id_cliente'),
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'id_user' => $id_user, // Relación con el usuario creado
                'identificacion' => $request->input('identificacion'),
                'telefono' => $request->input('telefono'),
                'correo' => $request->input('correo'),
                'estado' => 1,
                'fecha_creacion' => now(),
                'id_usuario' => $id_usuario,
                'ver_todas_localidades' => $request->boolean('ver_todas_localidades', false),
            ]);

            // Asociar localidades (muchos a muchos)
            $representante->localidades()->sync($request->input('id_localidad'));
    
            return response()->json([
                'ok' => true,
                'message' => 'Representante creado con éxito',
                'data' => $representante
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => 'No se pudo crear el representante'
            ], 500);
        }
    }

    public function actualizarRepresentante(Request $request, $id)
    {
        try {
            $id_usuario = auth()->user()->id ?? 1;

            $representante = Representante::findOrFail($id);

            $request->validate([
                    // 'id_cliente' => 'required|exists:cliente,id',
                    'nombre' => 'string|max:50',
                    'apellido' => 'string|max:50',
                    'identificacion' => [
                            'string',
                            'max:13',
                            'unique:representante,identificacion,' . $id . ',id', // Usamos el ID del representante
                        ],
                    'telefono' => 'string|max:15',
                    'correo' => 'email|max:255',
                    'id_localidad' => 'nullable|array',
                    'id_localidad.*' => 'exists:cliente_localidad,id'
                ],[
                    'identificacion.digits_between' => 'El número de Identificación debe tener entre 10 y 13 dígitos.',
                    'identificacion.unique' => 'El número de Identificación ingresado ya está registrado'
                ]
            );

            if ($request->has('id_localidad')) {//Si tiene localidades al actualizar se setea en 0 "ver_todas_localidades"
                $request->merge(['ver_todas_localidades' => 0]);
            }

            $representante->update([
                'nombre' => $request->input('nombre') ?? $representante->nombre,
                'apellido' => $request->input('apellido') ?? $representante->apellido,
                'identificacion' => $request->input('identificacion') ?? $representante->identificacion,
                'telefono' => $request->input('telefono') ?? $representante->telefono,
                'correo' => $request->input('correo') ?? $representante->correo,
                'estado' => $request->input('estado') ?? $representante->estado,
                'fecha_creacion' => $request->input('fecha_creacion') ?? $representante->fecha_creacion,
                'ver_todas_localidades' => $request->input('ver_todas_localidades') ?? $representante->ver_todas_localidades,
                'fecha_actualizacion' => now(),  // Asigna la fecha actual directamente
                'id_usuario_auditor' => $id_usuario,  // Asigna el id del usuario auditor
            ]);

            if ($request->has('id_localidad')) {
                $representante->localidades()->sync($request->input('id_localidad', []));
            }

            return response()->json([
                'ok' => true,
                'message' => 'Representante actualizado con éxito',
                'data' => $representante
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al actualizar representante: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
