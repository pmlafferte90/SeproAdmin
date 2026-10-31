<?php

namespace App\Services;

use App\Models\ClaseExtintor;
use App\Models\ControlExtintorCli;
use App\Models\MarcaExtintor;
use App\Models\TamanoExtintor;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtintorService
{
    public function crearMarca(array $data)
    {
        try {
            return MarcaExtintor::create($data);
        } catch (Exception $e) {
            throw new Exception('Error al crear la Marca: ' . $e->getMessage());
        }
    }
    public function crearPeso(array $data)
    {
        try {
            return TamanoExtintor::create($data);
        } catch (Exception $e) {
            throw new Exception('Error al crear la peso: ' . $e->getMessage());
        }
    }
    public function crearClase(array $data)
    {
        try {
            return ClaseExtintor::create($data);
        } catch (Exception $e) {
            throw new Exception('Error al crear la clase: ' . $e->getMessage());
        }
    }

    public function PaginateMarcas($request)
    {
        try {
            $buscar = $request->input('buscar');
            $perPage = $request->input('perPage', 5);
    
            // Comienza la consulta en lugar de usar all()
            $query = MarcaExtintor::query();
    
            // Aplica la condición de búsqueda si está presente
            if ($request->filled('buscar')) {
                $query->where('marca_extintor.marca', 'like', "%{$buscar}%");
            }
    
            // Realiza la paginación en la consulta
            $marcas = $query->paginate($perPage)->appends([
                'buscar' => $buscar,
                'perPage' => $perPage,
            ]);
    
            return $marcas;
    
        } catch (Exception $e) {
            throw new Exception('Error en paginate de marcas: ' . $e->getMessage());
        }
    }

    public function PaginatePesos($request)
    {
        try {
            $buscar = $request->input('buscar');
            $perPage = $request->input('perPage', 5);
    
            // Comienza la consulta en lugar de usar all()
            $query = TamanoExtintor::query();
    
            // Aplica la condición de búsqueda si está presente
            if ($request->filled('buscar')) {
                $query->where('tamano_extintor.tamanio', 'like', "%{$buscar}%");
            }
    
            // Realiza la paginación en la consulta
            $marcas = $query->paginate($perPage)->appends([
                'buscar' => $buscar,
                'perPage' => $perPage,
            ]);
    
            return $marcas;
    
        } catch (Exception $e) {
            throw new Exception('Error en paginate de pesos: ' . $e->getMessage());
        }
    }
    public function PaginateClases($request)
    {
        try {
            $buscar = $request->input('buscar');
            $perPage = $request->input('perPage', 5);
    
            // Comienza la consulta en lugar de usar all()
            $query = ClaseExtintor::query();
    
            // Aplica la condición de búsqueda si está presente
            if ($request->filled('buscar')) {
                $query->where('clase_extintor.clase', 'like', "%{$buscar}%");
            }
    
            // Realiza la paginación en la consulta
            $marcas = $query->paginate($perPage)->appends([
                'buscar' => $buscar,
                'perPage' => $perPage,
            ]);
    
            return $marcas;
    
        } catch (Exception $e) {
            throw new Exception('Error en paginate de pesos: ' . $e->getMessage());
        }
    }

    public function getMarcas($filtros)
    {
        try {
            $query = MarcaExtintor::select(
                'id',
                'marca',
                'estado'
            );

            if (isset($filtros['estado'])) {
                $query->where('estado', $filtros['estado']);
            }
            return $query->get();

        } catch (Exception $e) {
            throw new Exception('Error al obtener marcas: ' . $e->getMessage());
        }
    }

    public function getPesos($filtros)
    {
        try {
            $query = TamanoExtintor::select(
                'id',
                'tamanio',
                'estado'
            );

            if (isset($filtros['estado'])) {
                $query->where('estado', $filtros['estado']);
            }
                return $query->get();

        } catch (Exception $e) {
            throw new Exception('Error al obtener pesos: ' . $e->getMessage());
        }
    }

    public function getClases($filtros)
    {
        try {
            $query = ClaseExtintor::select(
                'id',
                'clase',
                'estado'
            );

            if (isset($filtros['estado'])) {
                $query->where('estado', $filtros['estado']);
            }
            
            return $query->get();

        } catch (Exception $e) {
            throw new Exception('Error al obtener clases: ' . $e->getMessage());
        }
    }

    public function get_control_extintor($id_cliente, $id_localidad, $filtros)
    {
        try {

            $query = ControlExtintorCli::select(
                "cliente.razon_social",
                "control_extintor_cli.id",
                "control_extintor_cli.id_cliente_localidad",
                "cliente_localidad.nombre as localidad",
                "control_extintor_cli.fecha_revision",
                "control_extintor_cli.fecha_fabricacion",
                "control_extintor_cli.fecha_prueba_hidrostatica",                
                "control_extintor_cli.id_subarea_empresa",
                "area.nombre AS area_nombre",
                "sub_area.nombre AS ubicacion",
                "control_extintor_cli.codigo", 
                "control_extintor_cli.clase",
                "control_extintor_cli.marca",
                "control_extintor_cli.peso",
                "control_extintor_cli.unidad",
                "control_extintor_cli.mango AS id_mango",
                "control_extintor_cli.mano AS id_mano",
                "control_extintor_cli.seguro AS id_seguro",
                "control_extintor_cli.cilindro AS id_cilindro",
                "control_extintor_cli.area_seguridad AS id_area_seguridad",
                "control_extintor_cli.instructivo_uso AS id_instructivo_uso",
                "control_extintor_cli.instructivo_clasificacion AS id_instructivo_clasificacion",
                "instructivo_clasificacion_nomenclatura.descripcion as instructivo_clasificacion_des",
                "instructivo_clasificacion_nomenclatura.abreviatura as instructivo_clasificacion",
                "control_extintor_cli.flecha_ubicacion AS id_flecha_ubicacion",
                "flecha_ubicacion_nomenclatura.descripcion as flecha_ubicacion_des",
                "flecha_ubicacion_nomenclatura.abreviatura as flecha_ubicacion",
                "control_extintor_cli.silueta AS id_silueta",
                "silueta_nomenclatura.descripcion as silueta_des",
                "silueta_nomenclatura.abreviatura as silueta",                
                "control_extintor_cli.rectangulo_instalacion AS id_rectangulo_instalacion",
                "control_extintor_cli.hidrante AS id_hidrante",
                "hidrante_nomenclatura.descripcion as hidrante_des",
                "hidrante_nomenclatura.abreviatura as hidrante",
                "control_extintor_cli.dimension_vidrio",
                "control_extintor_cli.novedad_marbete",
                DB::raw("CASE WHEN mango.id = 0 THEN 'OK' ELSE mango_abreviatura.abreviatura END AS mango"),
                DB::raw("CASE WHEN mano.id = 0 THEN 'OK' ELSE mano_abreviatura.abreviatura END AS mano"),
                DB::raw("CASE WHEN seguro.id = 0 THEN 'OK' ELSE seguro_abreviatura.abreviatura END AS seguro"),
                DB::raw("CASE WHEN cilindro.id = 0 THEN 'OK' ELSE cilindro_abreviatura.abreviatura END AS cilindro"),
                "control_extintor_cli.fecha_recarga", 
                "control_extintor_cli.fecha_prox_recarga",
                DB::raw("CASE WHEN area_seguridad.id = 0 THEN 'OK' ELSE area_seguridad_abreviatura.abreviatura END AS area_seguridad"),
                DB::raw("CASE WHEN instructivo_uso.id = 0 THEN 'OK' ELSE instructivo_uso_abreviatura.abreviatura END AS instructivo_uso"),
                DB::raw("CASE WHEN rectangulo_instalacion.id = 0 THEN 'OK' ELSE rectangulo_instalacion_abreviatura.abreviatura END AS rectangulo_instalacion"),
                "control_extintor_cli.observaciones", 
                "mango_abreviatura.descripcion AS mango_des", 
                "mano_abreviatura.descripcion AS mano_des",
                "seguro_abreviatura.descripcion AS seguro_des", 
                "cilindro_abreviatura.descripcion AS cilindro_des",
                "area_seguridad_abreviatura.descripcion AS area_seguridad_des", 
                "instructivo_uso_abreviatura.descripcion AS instructivo_uso_des",
                "rectangulo_instalacion_abreviatura.descripcion AS rectangulo_instalacion_des", 
                "control_extintor_cli.imagen_observacion",
                "control_extintor_cli.fecha_creacion",
                "control_extintor_cli.id_cliente_localidad",
                "control_extintor_cli.estado"
            )
            ->join('cliente_localidad', 'cliente_localidad.id', '=', 'control_extintor_cli.id_cliente_localidad')
            ->join('cliente', 'cliente.id', '=', 'cliente_localidad.id_cliente')
            ->join('sub_area', 'sub_area.id', '=', 'control_extintor_cli.id_subarea_empresa')
            ->join('area', 'area.id', '=', 'sub_area.id_area')
            ->leftJoin('nomenclatura AS mango', 'mango.id', '=', 'control_extintor_cli.mango')
            ->leftJoin('nomenclatura AS mango_abreviatura', 'mango_abreviatura.id', '=', 'control_extintor_cli.mango')
            ->leftJoin('nomenclatura AS mano', 'mano.id', '=', 'control_extintor_cli.mano')
            ->leftJoin('nomenclatura AS mano_abreviatura', 'mano_abreviatura.id', '=', 'control_extintor_cli.mano')
            ->leftJoin('nomenclatura AS seguro', 'seguro.id', '=', 'control_extintor_cli.seguro')
            ->leftJoin('nomenclatura AS seguro_abreviatura', 'seguro_abreviatura.id', '=', 'control_extintor_cli.seguro')
            ->leftJoin('nomenclatura AS cilindro', 'cilindro.id', '=', 'control_extintor_cli.cilindro')
            ->leftJoin('nomenclatura AS cilindro_abreviatura', 'cilindro_abreviatura.id', '=', 'control_extintor_cli.cilindro')
            ->leftJoin('nomenclatura AS area_seguridad', 'area_seguridad.id', '=', 'control_extintor_cli.area_seguridad')
            ->leftJoin('nomenclatura AS area_seguridad_abreviatura', 'area_seguridad_abreviatura.id', '=', 'control_extintor_cli.area_seguridad')
            ->leftJoin('nomenclatura AS instructivo_uso', 'instructivo_uso.id', '=', 'control_extintor_cli.instructivo_uso')
            ->leftJoin('nomenclatura AS instructivo_uso_abreviatura', 'instructivo_uso_abreviatura.id', '=', 'control_extintor_cli.instructivo_uso')
            ->leftJoin('nomenclatura AS instructivo_clasificacion_nomenclatura', 'instructivo_clasificacion_nomenclatura.id', '=', 'control_extintor_cli.instructivo_clasificacion')
            ->leftJoin('nomenclatura AS flecha_ubicacion_nomenclatura', 'flecha_ubicacion_nomenclatura.id', '=', 'control_extintor_cli.flecha_ubicacion')
            ->leftJoin('nomenclatura AS silueta_nomenclatura', 'silueta_nomenclatura.id', '=', 'control_extintor_cli.silueta')            
            ->leftJoin('nomenclatura AS rectangulo_instalacion', 'rectangulo_instalacion.id', '=', 'control_extintor_cli.rectangulo_instalacion')
            ->leftJoin('nomenclatura AS rectangulo_instalacion_abreviatura', 'rectangulo_instalacion_abreviatura.id', '=', 'control_extintor_cli.rectangulo_instalacion')
            ->leftJoin('nomenclatura AS hidrante_nomenclatura', 'hidrante_nomenclatura.id', '=', 'control_extintor_cli.hidrante')
            ->where('control_extintor_cli.estado',1);

            if($id_cliente != 0){
                $query->where('cliente.id', $id_cliente);
            }
            
            if (!empty($id_localidad)) {
                $query->where('control_extintor_cli.id_cliente_localidad', $id_localidad);
            }
            
            $filtros['anio'] = $filtros['anio'] ?? date('Y');
            $query->whereYear('control_extintor_cli.fecha_revision', $filtros['anio']);
            
            if (!empty($filtros['mes'])) {
                $query->whereMonth('control_extintor_cli.fecha_revision', $filtros['mes']);
            }
            
            if(isset($filtros['proxima_recarga_desde'])){
                $query->where('control_extintor_cli.fecha_prox_recarga','>=', $filtros['proxima_recarga_desde']);
            }
            // Log::alert($query->tosql());
            //$resultado = $query->orderByDesc('control_extintor_cli.id')->get();
            $resultado = $query->with('observacionesNomenclatura')->orderByDesc('control_extintor_cli.id')->get();
            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error al obtener areas empresa: ' . $e->getMessage());
            throw $e;
        }
    } 

    public function getUltimoControl($id_cliente, $id_localidad)
    {
        try {
            $query = ControlExtintorCli::select(
                "cliente.razon_social",
                "cliente_localidad.nombre as localidad",
                "sub_area.nombre AS ubicacion", 
                "control_extintor_cli.id",
                "control_extintor_cli.id_cliente_localidad",
                "control_extintor_cli.id_subarea_empresa",
                "control_extintor_cli.fecha_revision",    
                "control_extintor_cli.codigo", 
                "control_extintor_cli.clase",
                "control_extintor_cli.marca",
                "control_extintor_cli.peso",
                "control_extintor_cli.unidad",
                "control_extintor_cli.mango AS id_mango",
                "control_extintor_cli.mano AS id_mano",
                "control_extintor_cli.seguro AS id_seguro",
                "control_extintor_cli.cilindro AS id_cilindro",
                "control_extintor_cli.fecha_recarga", 
                "control_extintor_cli.fecha_prox_recarga",
                "control_extintor_cli.fecha_fabricacion",
                "control_extintor_cli.fecha_prueba_hidrostatica",
                "control_extintor_cli.area_seguridad AS id_area_seguridad",
                "control_extintor_cli.instructivo_uso AS id_instructivo_uso",
                "control_extintor_cli.instructivo_clasificacion AS id_instructivo_clasificacion",
                "control_extintor_cli.flecha_ubicacion AS id_flecha_ubicacion",
                "control_extintor_cli.silueta AS id_silueta",
                "control_extintor_cli.hidrante AS id_hidrante",
                "control_extintor_cli.dimension_vidrio",
                "control_extintor_cli.rectangulo_instalacion AS id_rectangulo_instalacion",
                "control_extintor_cli.novedad_marbete",
                DB::raw("CASE WHEN mango.id = 0 THEN 'OK' ELSE mango_abreviatura.abreviatura END AS mango"),
                DB::raw("CASE WHEN mano.id = 0 THEN 'OK' ELSE mano_abreviatura.abreviatura END AS mano"),
                DB::raw("CASE WHEN seguro.id = 0 THEN 'OK' ELSE seguro_abreviatura.abreviatura END AS seguro"),
                DB::raw("CASE WHEN cilindro.id = 0 THEN 'OK' ELSE cilindro_abreviatura.abreviatura END AS cilindro"),
                DB::raw("CASE WHEN area_seguridad.id = 0 THEN 'OK' ELSE area_seguridad_abreviatura.abreviatura END AS area_seguridad"),
                DB::raw("CASE WHEN instructivo_uso.id = 0 THEN 'OK' ELSE instructivo_uso_abreviatura.abreviatura END AS instructivo_uso"),
                DB::raw("CASE WHEN rectangulo_instalacion.id = 0 THEN 'OK' ELSE rectangulo_instalacion_abreviatura.abreviatura END AS rectangulo_instalacion"),
                "control_extintor_cli.observaciones", 
                "mango_abreviatura.descripcion AS mango_des", 
                "mano_abreviatura.descripcion AS mano_des",
                "seguro_abreviatura.descripcion AS seguro_des", 
                "cilindro_abreviatura.descripcion AS cilindro_des",
                "area_seguridad_abreviatura.descripcion AS area_seguridad_des", 
                "instructivo_uso_abreviatura.descripcion AS instructivo_uso_des",
                "rectangulo_instalacion_abreviatura.descripcion AS rectangulo_instalacion_des", 
                "control_extintor_cli.imagen_observacion",
                "control_extintor_cli.fecha_creacion",
                "control_extintor_cli.id_cliente_localidad",
                "control_extintor_cli.estado"
            )
            ->join('cliente_localidad', 'cliente_localidad.id', '=', 'control_extintor_cli.id_cliente_localidad')
            ->join('cliente', 'cliente.id', '=', 'cliente_localidad.id_cliente')
            ->join('sub_area', 'sub_area.id', '=', 'control_extintor_cli.id_subarea_empresa')
            ->join('area', 'area.id', '=', 'sub_area.id_area')
            ->leftJoin('nomenclatura AS mango', 'mango.id', '=', 'control_extintor_cli.mango')
            ->leftJoin('nomenclatura AS mango_abreviatura', 'mango_abreviatura.id', '=', 'control_extintor_cli.mango')
            ->leftJoin('nomenclatura AS mano', 'mano.id', '=', 'control_extintor_cli.mano')
            ->leftJoin('nomenclatura AS mano_abreviatura', 'mano_abreviatura.id', '=', 'control_extintor_cli.mano')
            ->leftJoin('nomenclatura AS seguro', 'seguro.id', '=', 'control_extintor_cli.seguro')
            ->leftJoin('nomenclatura AS seguro_abreviatura', 'seguro_abreviatura.id', '=', 'control_extintor_cli.seguro')
            ->leftJoin('nomenclatura AS cilindro', 'cilindro.id', '=', 'control_extintor_cli.cilindro')
            ->leftJoin('nomenclatura AS cilindro_abreviatura', 'cilindro_abreviatura.id', '=', 'control_extintor_cli.cilindro')
            ->leftJoin('nomenclatura AS area_seguridad', 'area_seguridad.id', '=', 'control_extintor_cli.area_seguridad')
            ->leftJoin('nomenclatura AS area_seguridad_abreviatura', 'area_seguridad_abreviatura.id', '=', 'control_extintor_cli.area_seguridad')
            ->leftJoin('nomenclatura AS instructivo_uso', 'instructivo_uso.id', '=', 'control_extintor_cli.instructivo_uso')
            ->leftJoin('nomenclatura AS instructivo_uso_abreviatura', 'instructivo_uso_abreviatura.id', '=', 'control_extintor_cli.instructivo_uso')
            ->leftJoin('nomenclatura AS rectangulo_instalacion', 'rectangulo_instalacion.id', '=', 'control_extintor_cli.rectangulo_instalacion')
            ->leftJoin('nomenclatura AS rectangulo_instalacion_abreviatura', 'rectangulo_instalacion_abreviatura.id', '=', 'control_extintor_cli.rectangulo_instalacion')
            ->where('control_extintor_cli.estado', 1)
            ->where('cliente.id', $id_cliente); 

            // Filtro por localidad si se proporciona
            if (!empty($id_localidad)) {
                $query->where('control_extintor_cli.id_cliente_localidad', $id_localidad);
            }
            
            //return $query->orderByDesc('control_extintor_cli.id')->first();
            return $query->with('observacionesNomenclatura')->orderByDesc('control_extintor_cli.id')->first();;

        } catch (\Exception $e) {
            Log::error('Error al obtener último control extintor: ' . $e->getMessage());
            throw $e;
        }
    }


    public function get_proxima_recarga_extintor($id_cliente, $filtros)
    {
        try {
            $query = ControlExtintorCli::select(
                "cliente.razon_social",
                "control_extintor_cli.id", 
                "sub_area.codigo AS cod", 
                "sub_area.nombre AS ubicacion",
                "sub_area.clase", 
                "sub_area.marca", 
                "sub_area.peso", 
                "sub_area.unidad", 
                DB::raw("CASE WHEN mango.id = 0 THEN 'OK' ELSE mango_abreviatura.abreviatura END AS mango"),
                DB::raw("CASE WHEN mano.id = 0 THEN 'OK' ELSE mano_abreviatura.abreviatura END AS mano"),
                DB::raw("CASE WHEN seguro.id = 0 THEN 'OK' ELSE seguro_abreviatura.abreviatura END AS seguro"),
                DB::raw("CASE WHEN cilindro.id = 0 THEN 'OK' ELSE cilindro_abreviatura.abreviatura END AS cilindro"),
                "control_extintor_cli.fecha_recarga", 
                "control_extintor_cli.fecha_prox_recarga",
                "control_extintor_cli.novedad_marbete",
                DB::raw("CASE WHEN area_seguridad.id = 0 THEN 'OK' ELSE area_seguridad_abreviatura.abreviatura END AS area_seguridad"),
                DB::raw("CASE WHEN instructivo_uso.id = 0 THEN 'OK' ELSE instructivo_uso_abreviatura.abreviatura END AS instructivo_uso"),
                DB::raw("CASE WHEN rectangulo_instalacion.id = 0 THEN 'OK' ELSE rectangulo_instalacion_abreviatura.abreviatura END AS rectangulo_instalacion"),
                "control_extintor_cli.observaciones", 
                "mango_abreviatura.descripcion AS mango_des", 
                "mano_abreviatura.descripcion AS mano_des",
                "seguro_abreviatura.descripcion AS seguro_des", 
                "cilindro_abreviatura.descripcion AS cilindro_des",
                "area_seguridad_abreviatura.descripcion AS area_seguridad_des", 
                "instructivo_uso_abreviatura.descripcion AS instructivo_uso_des",
                "rectangulo_instalacion_abreviatura.descripcion AS rectangulo_instalacion_des", 
                "control_extintor_cli.imagen_observacion",
                "control_extintor_cli.fecha_creacion",
                "control_extintor_cli.estado"
            )
            ->join('cliente', 'cliente.id', '=', 'control_extintor_cli.id_cliente_localidad')
            ->join('sub_area', 'sub_area.id', '=', 'control_extintor_cli.id_subarea_empresa')
            ->join('area', 'area.id', '=', 'sub_area.id_area')
            ->leftJoin('nomenclatura AS mango', 'mango.id', '=', 'control_extintor_cli.mango')
            ->leftJoin('nomenclatura AS mango_abreviatura', 'mango_abreviatura.id', '=', 'control_extintor_cli.mango')
            ->leftJoin('nomenclatura AS mano', 'mano.id', '=', 'control_extintor_cli.mano')
            ->leftJoin('nomenclatura AS mano_abreviatura', 'mano_abreviatura.id', '=', 'control_extintor_cli.mano')
            ->leftJoin('nomenclatura AS seguro', 'seguro.id', '=', 'control_extintor_cli.seguro')
            ->leftJoin('nomenclatura AS seguro_abreviatura', 'seguro_abreviatura.id', '=', 'control_extintor_cli.seguro')
            ->leftJoin('nomenclatura AS cilindro', 'cilindro.id', '=', 'control_extintor_cli.cilindro')
            ->leftJoin('nomenclatura AS cilindro_abreviatura', 'cilindro_abreviatura.id', '=', 'control_extintor_cli.cilindro')
            ->leftJoin('nomenclatura AS area_seguridad', 'area_seguridad.id', '=', 'control_extintor_cli.area_seguridad')
            ->leftJoin('nomenclatura AS area_seguridad_abreviatura', 'area_seguridad_abreviatura.id', '=', 'control_extintor_cli.area_seguridad')
            ->leftJoin('nomenclatura AS instructivo_uso', 'instructivo_uso.id', '=', 'control_extintor_cli.instructivo_uso')
            ->leftJoin('nomenclatura AS instructivo_uso_abreviatura', 'instructivo_uso_abreviatura.id', '=', 'control_extintor_cli.instructivo_uso')
            ->leftJoin('nomenclatura AS rectangulo_instalacion', 'rectangulo_instalacion.id', '=', 'control_extintor_cli.rectangulo_instalacion')
            ->leftJoin('nomenclatura AS rectangulo_instalacion_abreviatura', 'rectangulo_instalacion_abreviatura.id', '=', 'control_extintor_cli.rectangulo_instalacion')
            ->where('control_extintor_cli.estado',1);
            if($id_cliente != 0){
                $query->where('cliente.id', $id_cliente);
            }
            
            if(isset($filtros['proxima_recarga_desde'])){
                $query->where('control_extintor_cli.fecha_prox_recarga','>=', $filtros['proxima_recarga_desde']);
            }
            // Log::alert($query->tosql());
            $resultado = $query->get();

            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error al get_proxima_recarga_extintor: ' . $e->getMessage());
            throw $e;
        }
    } 

    public function storeControlExtintor(array $data)
    {
        try{
            // Obtener ID del usuario autenticado
            $id_usuario = auth()->id() ?? 1;
            // Crear el registro de control de extintor
            $controlExtintor = new ControlExtintorCli();
            $controlExtintor->fecha_revision = $data['fecha_revision'];
            $controlExtintor->codigo = $data['codigo_control'];
            $controlExtintor->clase = $data['clase'];
            $controlExtintor->marca = $data['marca'];
            $controlExtintor->peso = $data['peso'];
            $controlExtintor->unidad = $data['unidad'];
            $controlExtintor->id_cliente_localidad = $data['localidad_control'];
            $controlExtintor->id_subarea_empresa = $data['subarea_control'];
            $controlExtintor->mango = $data['mango'];
            $controlExtintor->mano = $data['mano'];
            $controlExtintor->seguro = $data['seguro'];
            $controlExtintor->cilindro = $data['cilindro'];
            $controlExtintor->fecha_recarga = $data['fecha_recarga'];
            $controlExtintor->fecha_prox_recarga = $data['fecha_proxima_recarga'];
            $controlExtintor->fecha_fabricacion = $data['fecha_fabricacion'] ?: null;
            $controlExtintor->fecha_prueba_hidrostatica = $data['fecha_prueba_hidrostatica'] ?: null;
            $controlExtintor->area_seguridad = $data['area_seguridad'] ?: null;
            $controlExtintor->instructivo_uso = $data['instructivo'] ?: null;
            $controlExtintor->instructivo_clasificacion = $data['instructivo_clasificacion'] ?: null;
            $controlExtintor->flecha_ubicacion = $data['flecha_ubicacion'] ?: null;
            $controlExtintor->silueta = $data['silueta'] ?: null;
            $controlExtintor->rectangulo_instalacion = $data['rectangulo_instalacion'] ?: null ; 
            $controlExtintor->hidrante = $data['hidrante'];
            $controlExtintor->dimension_vidrio = $data['dimension_vidrio'] ?: null;
            $controlExtintor->novedad_marbete = $data['observaciones_marbete'];
            $controlExtintor->observaciones = $data['observaciones'];
            $controlExtintor->fecha_creacion = now();
            $controlExtintor->estado = 1;
            $controlExtintor->id_usuario = $id_usuario;

            if (isset($data['imagen'])) {
                // Leer el contenido del archivo
                $imageContent = file_get_contents($data['imagen']->getRealPath());
            
                // Convertir el contenido a base64
                $imageBase64 = base64_encode($imageContent);
            
                // Guardar la imagen en base64 en la base de datos
                $controlExtintor->imagen_observacion = $imageBase64;
            }
            $controlExtintor->save();

            if (isset($data['observaciones_generales'])) {
                $controlExtintor->observacionesNomenclatura()->sync($data['observaciones_generales']);
            }
            
            return response()->json([
                'ok' => true,
                'message' => 'Control creado con éxito',
                'data' => $controlExtintor
            ], 201);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'ok' => false,
                'error' => 'No se pudo crear el Control'
            ], 500);
        }
    }

    public function updateControlExtintor($id, array $input)
    {
        // Buscar el registro por ID
        $controlExtintor = ControlExtintorCli::findOrFail($id);
    
        // Asignar los valores, manteniendo los originales si no se envían nuevos
        $controlExtintor->id_cliente_localidad = $input['localidad_control'];
        $controlExtintor->id_subarea_empresa = $input['subarea_control'];
        $controlExtintor->fecha_revision = $input['fecha_revision'];
        $controlExtintor->codigo =$input['codigo_control'];
        $controlExtintor->clase = $input['clase'];
        $controlExtintor->marca = $input['marca'];
        $controlExtintor->peso = $input['peso'];
        $controlExtintor->unidad = $input['unidad'];
        $controlExtintor->mango = $input['mango'];
        $controlExtintor->mano = $input['mano'];
        $controlExtintor->seguro = $input['seguro'];
        $controlExtintor->cilindro = $input['cilindro'];
        $controlExtintor->fecha_recarga = $input['fecha_recarga'];
        $controlExtintor->fecha_prox_recarga = $input['fecha_proxima_recarga'];
        $controlExtintor->fecha_fabricacion = $input['fecha_fabricacion'] ?: null;
        $controlExtintor->fecha_prueba_hidrostatica = $input['fecha_prueba_hidrostatica'] ?: null;
        $controlExtintor->area_seguridad = $input['area_seguridad'] ?: null;
        $controlExtintor->instructivo_uso = $input['instructivo'] ?: null;
        $controlExtintor->flecha_ubicacion = $input['flecha_ubicacion'] ?: null;
        $controlExtintor->instructivo_clasificacion = $input['instructivo_clasificacion'] ?: null;
        $controlExtintor->silueta = $input['silueta'] ?: null;
        $controlExtintor->hidrante = $input['hidrante'];
        $controlExtintor->dimension_vidrio = $input['dimension_vidrio'] ?: null;
        $controlExtintor->rectangulo_instalacion = $input['rectangulo_instalacion'] ?: null;
        $controlExtintor->novedad_marbete = $input['observaciones_marbete'];
        $controlExtintor->observaciones = $input['observaciones'] ?: null;
        $controlExtintor->fecha_actualizacion = now();
    
        // Si se sube una nueva imagen, la convierte a base64, de lo contrario, mantiene la actual
        if (!empty($input['imagen'])) {
            $controlExtintor->imagen_observacion = base64_encode(file_get_contents($input['imagen']->getRealPath()));
        }
    
        $controlExtintor->save();

        $controlExtintor->observacionesNomenclatura()->sync($input['observaciones_generales'] ?? []);

        return $controlExtintor;

    }
    
    
}
