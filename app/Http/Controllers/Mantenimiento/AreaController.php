<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AreaEmpresa;
use App\Models\Cliente;
use App\Models\SubArea;
use App\Services\AreaService;
use App\Services\ClienteLocalidadService;
use App\Services\ClienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaController extends Controller
{
    protected $areaService, $clienteLocalidadService, $clienteService;

    public function __construct()
    {
        $this->areaService = new AreaService;
        $this->clienteLocalidadService = new ClienteLocalidadService;
        $this->clienteService = new ClienteService;

    }
    public function index(Request $request)
    {
        $areas = $this->areaService->obtenerAreas($request);

        return view('Submenus.area', compact('areas'));
    }
    public function get_sub_area(Request $request)
    {
        $filtros = $request->all(); // Obtiene todos los parámetros del request

        $subareas = $this->areaService->getSubAreas($filtros);

        $areas = Area::where('estado',1)->get();
        return view('Submenus.subarea', compact('subareas','areas'));
    }
    public function store(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'nombre' => 'required|string|max:255',
            ]);
            $rol = auth()->user()->rol;

            // Crear un nuevo area
            area::create([
                'nombre' => $request->input('nombre'),
                'estado' => (($rol == 1 || $rol == 2) ? 1 : 2),
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Area creada exitosamente.'
            ]);
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $area = Area::find($id);
            $area->update([
                'nombre' => $request->input('nombre') ?? $area->nombre,
                'estado' => $request->input('estado') ?? $area->estado,
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'Area actualizada exitosamente.'
            ]);
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
        
    }
    public function cambiarEstado($id, Request $request)
    {
        try{
            $area = Area::find($id);

            if ($area) {
                $area->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                $area->save(); // Guardar los cambios

                if ($area->estado == 0) {
                    // Obtener las subáreas asociadas a esta área
                    $sub_areas = $this->areaService->getSubAreas(['id_area'=>$area->id]);
                    
                    // Inactivar todas las subáreas relacionadas
                    foreach ($sub_areas as $subarea) {
                        $subarea->estado = 0; 
                        $subarea->save(); 
                    }
                }

                $mensaje = $area->estado == 1 ? 'El area ha sido restaurada exitosamente.' : 'El area ha sido inactivada exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }
    public function cambiarEstado_subarea($id, Request $request)
    {
        try{
            $subarea = SubArea::find($id);

            if ($subarea) {
                $subarea->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                $subarea->save(); // Guardar los cambios

                $mensaje = $subarea->estado == 1 ? 'El subarea ha sido restaurada exitosamente.' : 'El subarea ha sido inactivada exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }

    public function crear_subarea(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'nombre' => 'required|string|max:255',
                'area_id' => 'required|integer|exists:area,id', // Aseguramos que el área exista
                'codigo' => 'required|string|max:255',
                'clase' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'peso' => 'required|numeric',
                'unidad' => 'required|string|max:255',
            ]);

            $rol = auth()->user()->rol;

            $this->areaService->crearSubArea([
                'nombre' => $request->input('nombre'),
                'id_area' => $request->input('area_id'), // Recogemos el id del select
                'codigo' => $request->input('codigo'),
                'clase' => $request->input('clase'),
                'marca' => $request->input('marca'),
                'peso' => $request->input('peso'),
                'unidad' => $request->input('unidad'),
                'estado' => (($rol == 1 || $rol == 2) ? 1 : 2),
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Área creada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizar_subarea(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'string|max:255|nullable',
                'area_id' => 'integer|exists:area,id|nullable', // Validar que el área exista si está presente
                'codigo' => 'required|string|max:255',
                'clase' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'peso' => 'required|numeric',
                'unidad' => 'required|string|max:255',

            ]);

            // Llamar al servicio para actualizar un área
            $this->areaService->editarSubArea($id, [
                'nombre' => $request->input('nombre'),
                'id_area' => $request->input('area_id'), // Recogemos el id del select si está presente
                'codigo' => $request->input('codigo'),
                'clase' => $request->input('clase'),
                'marca' => $request->input('marca'),
                'peso' => $request->input('peso'),
                'unidad' => $request->input('unidad'),
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Área actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    //FUNCION INDEX DEL MANTENIMIENTO DE AREAS EMPRESAS
    public function obtener_areas_empresa(Request $request,$id_cliente)
    {
        try {
            //Areas Empresa (Localidad)
            $areaEmpresas = $this->areaService->get_areas_empresa($request, $id_cliente);
            //datos para la modal
            // Obtener Áreas y Subáreas activas
            //$areas = $this->areaService->obtenerAreasSubareasActivas();
            // Obtener Clientes Activos
            //$clientes = $this->clienteService->obtenerClientesActivos($id_cliente);

            return view('Submenus.area_empresa', compact('areaEmpresas'/*,'areas'*/,'id_cliente'/*,'clientes'*/));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function crear_area_empresa(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'cliente' => 'required|numeric|exists:cliente,id',
                'subarea' => 'required|numeric|exists:sub_area,id', // Aseguramos que el área exista
            ]);

            $rol = auth()->user()->rol;


            $this->areaService->crearAreaEmpresa([
                'id_empresa' => $request->input('cliente'),
                'id_subarea' => $request->input('subarea'), // Recogemos el id del select
                'estado' => (($rol == 1 || $rol == 2) ? 1 : 2),
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Área creada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function cambiarEstado_area_empresa($id, Request $request)
    {
        try{
            $area_empresa = AreaEmpresa::find($id);

            if ($area_empresa) {
                $rol = auth()->user()->rol;
                $estado_actual = $area_empresa->estado;
                $accion = $request->input('accion');

                if ($rol == 1 || $rol == 2) {
                    // (administrador)
                    if ($estado_actual == 0 && $accion == 'restaurar') {
                        $area_empresa->estado = 1;
                    } elseif ($estado_actual == 1 && $accion == 'inactivar') {
                        $area_empresa->estado = 0;
                    } elseif ($estado_actual == 2 && $accion == 'aprobar') {
                        $area_empresa->estado = 1; // Aprobar (Activo)
                    } elseif ($estado_actual == 2 && $accion == 'rechazar') {
                        $area_empresa->estado = 0; // Rechazar (Inactivo)
                    } elseif ($estado_actual == 3 && $accion == 'aprobar') {
                        $area_empresa->estado = 0; // Rechazar (Inactivo)
                    } elseif ($estado_actual == 3 && $accion == 'rechazar') {
                        $area_empresa->estado = 1; // Aprobar (Activo)
                    }
                } else {
                    //  otros roles
                    if ($estado_actual == 0 && $accion == 'restaurar') {
                        $area_empresa->estado = 2;
                    } elseif ($estado_actual == 1 && $accion == 'inactivar') {
                        $area_empresa->estado = 3;
                    }
                }

                $area_empresa->save();

                return response()->json([
                    'success' => true,
                    'message' => 'El estado del área ha sido actualizado correctamente.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getSubareasByLocalidad($id_localidad)
    {
        try {
            $sub_areas_localidad = $this->areaService->getSubareasByLocalidad($id_localidad);

            $data_sub_areas_localidad = json_decode($sub_areas_localidad->getContent(), true);

            if (!$data_sub_areas_localidad['ok']) {
                throw new \Exception($data_sub_areas_localidad['error']);
            }
            
            return response()->json([
                'ok' => true, 
                'data' => $data_sub_areas_localidad['data']
            ]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function asignarSubareasALocalidad(Request $request)
    {
        DB::beginTransaction();
        
        try {
            Log::info("request subareas");
            Log::info(collect($request));
            $id_localidad = $request->input('localidad');
            $subareas = $request->input('subareas');
            $id_usuario = auth()->user()->id ?? 1;

            //subareas activas de la localidad
            $subareasExistentes = AreaEmpresa::where('id_cliente_localidad', $id_localidad)
                ->where('estado', 1)
                ->pluck('id_subarea') // Obtenemos solo los IDs de las sub-áreas existentes
                ->toArray();
                Log::info("subareas subareas");
                Log::info(collect($subareas));
            Log::info("subareas ex");
            Log::info(collect($subareasExistentes));
            $subareasDesactivar = array_diff($subareasExistentes, $subareas); // Encuentra las sub-áreas a desactivar
            Log::info("subareas subareasDesactivar");
            Log::info(collect($subareasDesactivar));
            if (!empty($subareasDesactivar)) {
                AreaEmpresa::whereIn('id_subarea', $subareasDesactivar)
                    ->where('id_cliente_localidad', $id_localidad)
                    ->update([
                        'estado' => 0,
                        'fecha_actualizacion' => now(),
                        'id_usuario_auditor' => $id_usuario
                    ]); // Desactivar (estado 0)
            }
    
            //Insertar las nuevas sub-áreas, si no están ya asignadas
            foreach ($subareas as $subarea_id) {
                if (!in_array($subarea_id, $subareasExistentes)) {
                    // Llamamos al servicio para crear la relación de sub-área con localidad
                    $this->areaService->crearAreaEmpresa([
                        'id_localidad' => $id_localidad,
                        'id_subarea' => $subarea_id,
                    ]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'ok' => true,
                'message' => 'Sub-áreas asignadas correctamente.',
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('asignarSubareasALocalidad ' . $e->getMessage());
    
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkExistRelationAreaEmpresa(int $id_localidad)
    {
        try {
            $serviceResponse = $this->areaService->checkExistRelationAreaEmpresa($id_localidad);
            $payload = json_decode($serviceResponse->getContent(), true);

            if (! $payload['ok']) {
                // No hay sub-áreas activas para esa localidad
                return response()->json([
                    'ok'    => false,
                    'error' => $payload['error']
                ], 404);
            }

            return response()->json([
                'ok'   => true,
                'data' => $payload['data']
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error en checkExistRelationAreaEmpresa [{$id_localidad}]: {$e->getMessage()}");
            return response()->json([
                'ok'    => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function subareas_activas_api()
    {
        try {
            $areas = $this->areaService->obtenerAreasSubareasActivas();

            // Convertir a formato deseado para JS
            $resultado = [];

            foreach ($areas as $area) {
                foreach ($area->subareas as $sub) {
                    $resultado[] = [
                        'id' => $sub->id,
                        'text' => $sub->nombre,
                        'area' => $area->nombre,
                        'codigo' => $sub->codigo ?? null
                    ];
                }
            }

            return response()->json(['data' => $resultado]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
}
