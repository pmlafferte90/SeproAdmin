<?php

namespace App\Services;

use App\Models\Area;
use App\Models\AreaEmpresa;
use App\Models\ClienteLocalidad;
use App\Models\SubArea;
use Exception;
use Illuminate\Support\Facades\Log;

class AreaService
{
    public function obtenerAreas($request)
    {
        try {
            $buscar = $request->input('buscar');
            $perPage = $request->input('perPage', 5);

            $query = Area::query();
            if(isset($request->estado)){
                $query = $query->where("estado",$request->estado);
            }
            if ($request->filled('buscar')) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%");
                });
            }

            $areas = $query->paginate($perPage)->appends([
                'buscar' => $buscar,
                'perPage' => $perPage,
            ]);

            return $areas;
        } catch (\Exception $e) {
            Log::error('Error al obtener areas: ' . $e->getMessage());
            throw $e;
        }
    }

    public function crearArea(array $data)
    {
        try {
            return Area::create($data);
        } catch (Exception $e) {
            throw new Exception('Error al crear el área: ' . $e->getMessage());
        }
    }

    public function editarArea($id, array $data)
    {
        try {
            $area = Area::findOrFail($id); // Verificar que el área existe
            $area->update($data);
            return $area;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar el área: ' . $e->getMessage());
        }
    }
    public function crearSubArea(array $data)
    {
        try {
            return SubArea::create($data);
        } catch (Exception $e) {
            throw new Exception('Error al crear el área: ' . $e->getMessage());
        }
    }

    public function editarSubArea($id, array $data)
    {
        try {
            $subarea = SubArea::findOrFail($id); // Verificar que el área existe

            $subarea->update($data);

            return $subarea;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar el área: ' . $e->getMessage());
        }
    }
    public function get_areas_empresa($request, $id_cliente)
    {
        try {

            $query = AreaEmpresa::select(
                    'areas_empresa.id',
                    'cliente.id as id_cliente',
                    'cliente_localidad.nombre AS nombre_localidad',
                    'area.id as id_area',
                    'sub_area.id as id_subarea',
                    'cliente.razon_social as cliente',
                    'sub_area.nombre as subarea',
                    'area.nombre as area',
                    'areas_empresa.estado',
                    'areas_empresa.fecha_creacion'
                )
                ->join('cliente_localidad', 'cliente_localidad.id', '=', 'areas_empresa.id_cliente_localidad')
                ->join('cliente',           'cliente.id',           '=', 'cliente_localidad.id_cliente')
                ->join('sub_area',          'sub_area.id',          '=', 'areas_empresa.id_subarea')
                ->join('area',              'area.id',              '=', 'sub_area.id_area')
                ->where('areas_empresa.estado', 1)
                ->where('area.estado',           1)
                ->where('sub_area.estado',       1);

            if ($id_cliente > 0) {
                $query->where('cliente.id', $id_cliente);
            }

            if ($buscar = $request->input('buscar')) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('cliente.razon_social', 'like', "%{$buscar}%")
                      ->orWhere('sub_area.nombre',     'like', "%{$buscar}%")
                      ->orWhere('area.nombre',         'like', "%{$buscar}%");
                });
            }

            $perPage = $request->input('perPage', 5);
            $areas   = $query
                ->orderByRaw('cliente.id, cliente_localidad.id, sub_area.id')
                ->paginate($perPage)
                ->appends($request->only(['buscar', 'perPage']));

            return $areas;
        } catch (\Exception $e) {
            Log::error('Error al obtener areas empresa: ' . $e->getMessage());
            throw $e;
        }
    } 

    public function get_areas_by_cliente($id_cliente)
    {
        try {

            if ($id_cliente == 0) {
                // Realizar alguna acción en caso de que el id_cliente sea 0
                // Por ejemplo, puedes obtener todos los registros sin filtrar por id_empresa.
                $areas = Area::where('area.estado', 1)
                            ->with(['subAreas' => function($query) {
                                $query->where("sub_area.estado", 1); // Filtrar solo subáreas en estado 1
                            }])
                            ->get();
            } else {
                // Consulta original si id_cliente no es 0
                $areas = Area::where('area.estado', 1)
                            ->with(['subAreas' => function($query) {
                                $query->where("sub_area.estado", 1); // Filtrar solo subáreas en estado 1
                            }])
                            ->whereHas('subAreas.areasEmpresa', function ($query) use ($id_cliente) {
                                $query->join('cliente_localidad', 'areas_empresa.id_cliente_localidad', '=', 'cliente_localidad.id')
                                      ->join('cliente', 'cliente_localidad.id_cliente', '=', 'cliente.id')
                                      ->where('cliente.id', $id_cliente)
                                      ->where("areas_empresa.estado", 1); // Filtrar por estado activo
                            })
                            ->get();
            }
            
            
            // dd($areas);

            return $areas;
        } catch (\Exception $e) {
            Log::error('Error al obtener areas por cliente: ' . $e->getMessage());
            throw $e;
        }
    }

    public function crearAreaEmpresa(array $data)
    {
        try {
            // Obtener el rol del usuario autenticado
            $usuario = auth()->user();
            $id_usuario = $usuario->id ?? 1;
            $rol = $usuario ? $usuario->rol : 1; // Usamos 3 como un valor predeterminado (o el rol que desees)
            $estado = ($rol == 1 || $rol == 2) ? 1 : 2;

            $area_empresa = AreaEmpresa::create([
                'id_cliente_localidad' => $data['id_localidad'],
                'id_subarea' => $data['id_subarea'],
                'id_usuario' => $id_usuario,
                'estado' => $estado,
                'fecha_creacion' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Subarea - Localidad registrada con éxito',
                'data' => $area_empresa
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear Subarea - Localidad: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSubAreas($filtros)
    {
        try {
            $query = SubArea::select(
                'sub_area.id',
                'sub_area.nombre',
                'sub_area.codigo',
                'sub_area.clase',
                'sub_area.marca',
                'sub_area.peso',
                'sub_area.unidad',
                'sub_area.id_area',
                'sub_area.estado',
                'area.nombre AS area'
            )
            ->join('area', 'area.id', '=', 'sub_area.id_area')
            ->orderBy('area.id', 'ASC');
    
            // Filtrado por id_area si se especifica
            if (isset($filtros['id_area'])) {
                $query->where('sub_area.id_area', $filtros['id_area']);
            }
            if (isset($filtros['buscar'])) {
                $query->where(function ($q) use ($filtros) {
                    $q->where('sub_area.nombre', 'like', "%{$filtros['buscar']}%")
                      ->orWhere('sub_area.codigo', 'like', "%{$filtros['buscar']}%")
                      ->orWhere('sub_area.clase', 'like', "%{$filtros['buscar']}%")
                      ->orWhere('sub_area.marca', 'like', "%{$filtros['buscar']}%")
                      ->orWhere('sub_area.peso', 'like', "%{$filtros['buscar']}%")
                      ->orWhere('sub_area.unidad', 'like', "%{$filtros['buscar']}%")
                      ->orWhere('area.nombre', 'like', "%{$filtros['buscar']}%");
                });
            }
            // Comprobar si se requiere paginación
            $perPage = isset($filtros['perPage']) ? $filtros['perPage'] : 5; // Definir un valor predeterminado
            return $query->paginate($perPage); // Paginación
        } catch (Exception $e) {
            throw new Exception('Error al obtener el subárea: ' . $e->getMessage());
        }
    }

    public function getSubareasByLocalidad($id_localidad)
    {
        try {

            $localidades = AreaEmpresa::select(
                'areas_empresa.id',
                'area.nombre AS nombre_area',
                'sub_area.id AS id_subarea',
                'area.id AS id_area',
                'sub_area.nombre',
                'sub_area.codigo',
                'sub_area.clase',
                'sub_area.marca',
                'sub_area.peso',
                'sub_area.unidad'
            )
            ->join('sub_area','sub_area.id','=','areas_empresa.id_subarea')
            ->join('area','area.id','=','sub_area.id_area')
            ->where("id_cliente_localidad", $id_localidad)
            ->where('areas_empresa.estado',1)
            ->where('sub_area.estado',1)
            ->get();

            return response()->json([
                'ok' => true,
                'data' => $localidades
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al obtener sub áreas por localidad: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'error' =>  $e->getMessage()
            ], 500);
        }
    }

    public function obtenerAreasSubareasActivas()
    {
        try {

            $areas = Area::with(['subAreas' => function($query) {
                $query->where('estado', 1); // Solo subáreas activas
            }])
            ->where('estado', 1)  // Solo áreas activas
            ->get();

            return $areas;

        } catch (\Exception $e) {
           Log::error('Error al obtener areas y subareas activas: ' . $e->getMessage());
           return $e->getMessage();
        }
    }
    

 public function checkExistRelationAreaEmpresa($id_localidad)
    {
        try {
            $existe = AreaEmpresa::from('areas_empresa')
                                ->join('sub_area', 'sub_area.id', '=', 'areas_empresa.id_subarea')
                                ->join('area', 'area.id', '=', 'sub_area.id_area')
                                ->where('areas_empresa.id_cliente_localidad', $id_localidad)
                                ->where('area.estado', 1)
                                ->where('sub_area.estado', 1)
                                ->where('areas_empresa.estado', 1)
                                ->exists();

            if (! $existe) {
                return response()->json([
                    'ok'    => false,
                    'error' => 'No se encontraron subareas para esa localidad'
                ], 404);
            }

            return response()->json([
                'ok'   => true,
                'data' => 'Existen subareas asignadas para esa localidad'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al obtener sub-áreas por localidad [{$id_localidad}]: {$e->getMessage()}");
            return response()->json([
                'ok'    => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function countSubareasByLocalidad(int $idLocalidad): int
    {
        try {
            return AreaEmpresa::query()
                ->where('id_cliente_localidad', $idLocalidad)
                ->where('areas_empresa.estado', 1)
                ->whereHas('subArea.area', function ($q) {
                    $q->where('estado', 1);
                })
                ->count();
        } catch (\Exception $e) {
            Log::error(
                "Error al contar subáreas por localidad [{$idLocalidad}]: " 
                . $e->getMessage()
            );
            throw $e;
        }
    }

}
