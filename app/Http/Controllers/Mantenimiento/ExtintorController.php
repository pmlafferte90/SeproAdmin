<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Exports\ControlExtintorExport;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\ClaseExtintor;
use App\Models\Cliente;
use App\Models\ControlExtintorCli;
use App\Models\MarcaExtintor;
use App\Models\TamanoExtintor;
use App\Services\AreaService;
use App\Services\ClienteService;
use App\Services\ExtintorService;
use App\Services\NomenclaturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class 
ExtintorController extends Controller
{
    protected $extintorService, $nomenclaturaService, $areaService, $clienteService;


    public function __construct(ExtintorService $extintorService)
    {
        $this->extintorService = $extintorService;
        $this->nomenclaturaService = new NomenclaturaService;
        $this->areaService = new AreaService;
        $this->clienteService = new ClienteService;

    }
    public function index()
    {
    }
    
    public function get_marca(Request $request)
    {
        $marcas = $this->extintorService->PaginateMarcas($request);

        return view('Submenus.marca', compact('marcas'));
    }
    public function get_peso(Request $request)
    {
        $pesos = $this->extintorService->PaginatePesos($request);
        return view('Submenus.peso', compact('pesos'));
    }
    public function get_clase(Request $request)
    {
        $clases = $this->extintorService->PaginateClases($request);
        return view('Submenus.clase', compact('clases'));
    }
    public function insertar_marca(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'marca' => 'required|string|max:255',
            ]);

            // Llamar al servicio para crear un área
            $this->extintorService->crearMarca([
                'marca' => $request->input('marca'),
                'estado' => 1,
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Marca creada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function insertar_peso(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'peso' => 'required|numeric',
            ]);

            // Llamar al servicio para crear un área
            $this->extintorService->crearPeso([
                'tamanio' => $request->input('peso'),
                'estado' => 1,
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Peso creado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function insertar_clase(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'clase' => 'required|string|max:255',
            ]);

            // Llamar al servicio para crear un área
            $this->extintorService->crearClase([
                'clase' => $request->input('clase'),
                'estado' => 1,
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Clase creada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function cambiarEstado_marca($id, Request $request)
    {
        try{
            $marca = MarcaExtintor::find($id);

            if ($marca) {
                $marca->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                $marca->save(); // Guardar los cambios

                $mensaje = $marca->estado == 1 ? 'La marca ha sido restaurada exitosamente.' : 'La marca ha sido inactivada exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }
    public function cambiarEstado_peso($id, Request $request)
    {
        try{
            $peso = TamanoExtintor::find($id);

            if ($peso) {
                $peso->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                $peso->save(); // Guardar los cambios

                $mensaje = $peso->estado == 1 ? 'El peso ha sido restaurado exitosamente.' : 'El peso ha sido inactivado exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }
    public function cambiarEstado_clase($id, Request $request)
    {
        try{
            $clase = ClaseExtintor::find($id);

            if ($clase) {
                $clase->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                $clase->save(); // Guardar los cambios

                $mensaje = $clase->estado == 1 ? 'La clase ha sido restaurada exitosamente.' : 'La clase ha sido inactivada exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }    

    public function obtener_control_extintor_por_cliente($id_cliente, array $filtros = [])
    {
        try {

            $filtros = request()->only(['mes', 'anio']);
            $id_localidad = request()->query('localidad');

            $controlExtintores = $this->extintorService->get_control_extintor($id_cliente, $id_localidad, $filtros);
            $ultimoControlExtintor = $this->extintorService->getUltimoControl($id_cliente, $id_localidad);

            $areas = $this->areaService->get_areas_by_cliente($id_cliente);

            $clases = $this->extintorService->getClases(['estado'=>1]);
            $marcas = $this->extintorService->getMarcas(['estado'=>1]);
            $pesos = $this->extintorService->getPesos(['estado'=>1]);
            $nomenclaturas = $this->nomenclaturaService->getNomenclaturasByTipo(['estado'=>1]);

            $clientes = $this->clienteService->obtenerClientesActivos($id_cliente);


            return view('Submenus.control_extintor', compact('controlExtintores', 'id_cliente','areas','clases','marcas','pesos', 'nomenclaturas','clientes', 'ultimoControlExtintor'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function obtener_control_extintores(array $filtros = [])
    {
        try {
            $id_rol = auth()->user()->rol;
            $filtros = request()->only(['mes', 'anio']); 

            $id_cliente = (!in_array($id_rol, [1, 2, 3])) ? auth()->user()->id_cliente : 0;

            $controlExtintores = $this->extintorService->get_control_extintor($id_cliente, $filtros);

            $areas = $this->areaService->get_areas_by_cliente($id_cliente);

            $clases = $this->extintorService->getClases(['estado'=>1]);
            $marcas = $this->extintorService->getMarcas(['estado'=>1]);
            $pesos = $this->extintorService->getPesos(['estado'=>1]);
            $nomenclaturas = $this->nomenclaturaService->getNomenclaturasByTipo(['estado'=>1]);
            // Log::alert(json_encode($nomenclaturas,JSON_PRETTY_PRINT));

            $clientes = $this->clienteService->obtenerClientesActivos($id_cliente);

            return view('Submenus.control_extintor', compact('controlExtintores', 'id_cliente','areas','clases','marcas','pesos', 'nomenclaturas', 'clientes'));

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    



    public function insertar_control_extintor(Request $request)
    {
        DB::beginTransaction();
        try{
            // Log::alert(json_encode($request,JSON_PRETTY_PRINT));
            $validatedData = $request->validate([
                // 'id_cliente' => 'required|exists:cliente,id',
                'localidad_control' => 'required|exists:cliente_localidad,id',
                'fecha_revision' => 'required|date',
                'codigo_control' => 'required|string',
                'clase' => 'required|string',
                'marca' => 'required|string',
                'peso' => 'required|numeric',
                'unidad' => 'required|string',
                'subarea_control' => 'required|exists:sub_area,id',
                'mango' => 'required|exists:nomenclatura,id',
                'mano' => 'required|exists:nomenclatura,id',
                'seguro' => 'required|exists:nomenclatura,id',
                'cilindro' => 'required|exists:nomenclatura,id',
                'fecha_recarga' => 'required|date',
                'fecha_proxima_recarga' => 'required|date|after_or_equal:fecha_recarga',
                'fecha_fabricacion' => 'nullable|date',
                'fecha_prueba_hidrostatica' => 'nullable|date',
                'area_seguridad' => 'nullable|exists:nomenclatura,id',
                'instructivo' => 'nullable|exists:nomenclatura,id',
                'instructivo_clasificacion' => 'nullable|exists:nomenclatura,id',
                'flecha_ubicacion' => 'nullable|exists:nomenclatura,id',
                'silueta' => 'nullable|exists:nomenclatura,id',
                'rectangulo_instalacion' => 'nullable|exists:nomenclatura,id',
                'hidrante' => 'required|exists:nomenclatura,id',
                'dimension_vidrio' => 'nullable|string',
                'observaciones_marbete' => 'required|string',
                'observaciones' => 'nullable|string',
                'observaciones_generales' => 'nullable|array',
                'observaciones_generales.*' => 'exists:nomenclatura,id',
                'imagen' => 'nullable|image|max:2048', // Limitar a 2MB
            ]);
            // Llamar al servicio para almacenar los datos
            $result = $this->extintorService->storeControlExtintor($validatedData);
            $control_ext = json_decode($result->getContent(), true);

            if (!$control_ext['ok']) {
                throw new \Exception($control_ext['error']);
            }

            // Si todo es correcto, confirmar la transacción
            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Representante creado exitosamente',
                'data' => $control_ext['data']
            ], 201);
        } catch (\Exception $e) {
            // Si ocurre un error, deshacer la transacción
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al crear el control de extintor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update_control_extintor(Request $request, $id)
    {
        try {

            // Validar los datos del formulario
            $validatedData = $request->validate([
                'localidad_control' => 'required|exists:cliente_localidad,id',
                'subarea_control' => 'required|exists:sub_area,id',
                'fecha_revision' => 'required|date',
                'codigo_control' => 'required|string',
                'clase' => 'required|string',
                'marca' => 'required|string',
                'peso' => 'required|numeric',
                'unidad' => 'required|string',    
                'mango' => 'required|exists:nomenclatura,id',
                'mano' => 'required|exists:nomenclatura,id',
                'seguro' => 'required|exists:nomenclatura,id',
                'cilindro' => 'required|exists:nomenclatura,id',
                'fecha_recarga' => 'required|date',
                'fecha_proxima_recarga' => 'required|date|after_or_equal:fecha_recarga',
                'fecha_fabricacion' => 'nullable|date',
                'fecha_prueba_hidrostatica' => 'nullable|date',
                'area_seguridad' => 'nullable|exists:nomenclatura,id',
                'instructivo' => 'nullable|exists:nomenclatura,id',
                'instructivo_clasificacion' => 'nullable|exists:nomenclatura,id',
                'flecha_ubicacion' => 'nullable|exists:nomenclatura,id',
                'silueta' => 'nullable|exists:nomenclatura,id',
                'rectangulo_instalacion' => 'nullable|exists:nomenclatura,id',
                'hidrante' => 'required|exists:nomenclatura,id',
                'dimension_vidrio' => 'nullable|string',
                'observaciones_marbete' => 'required|string',
                'observaciones' => 'nullable|string',
                'observaciones_generales' => 'nullable|array',
                'observaciones_generales.*' => 'exists:nomenclatura,id',
                'imagen' => 'nullable|image|max:2048', // Limitar a 2MB
            ]);

            $resultado = $this->extintorService->updateControlExtintor($id, $validatedData);

            return response()->json([
                'ok' => true,
                'message' => 'Representante creado exitosamente',
                'data' => $resultado
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al crear el control de extintor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function cambiarEstado_control_extintores($id, Request $request)
    {
        try{
            $control = ControlExtintorCli::find($id);

            if ($control) {
                $control->estado = $request->input('estado'); // Actualizar el estado según lo enviado
       
                $control->save(); // Guardar los cambios

                $mensaje = $control->estado == 1 ? 'El control ha sido restaurado exitosamente.' : 'El control ha sido inactivado exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }

    public function export(Request $request)
    {
        
        // Obtener los datos filtrados que se muestran en la vista
        $filtros = request()->only(['mes', 'anio']);
        $empresa = $this->clienteService->getClienteById($request->id_cliente);
        $data_controlExtintores = $this->extintorService->get_control_extintor($request->id_cliente, $filtros);
        $controlExtintores = [];

        foreach ($data_controlExtintores as $item) {
            $ubicacion = $item['ubicacion'];
            if (!isset($controlExtintores[$ubicacion])) {
                $controlExtintores[$ubicacion] = [];
            }
            $controlExtintores[$ubicacion][] = $item;
        }
        //Log::alert("empresa controlExtintores");
        //Log::alert(json_encode($controlExtintores,JSON_PRETTY_PRINT));

        // Pasar los datos a la exportación
        return Excel::download(new ControlExtintorExport($controlExtintores, $empresa), 'control_extintores.xlsx');
    }

}
