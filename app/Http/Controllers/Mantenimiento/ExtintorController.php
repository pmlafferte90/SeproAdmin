<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Exports\ControlExtintorExport;
use App\Exports\ResumenExtintoresExport;
use App\Http\Controllers\Controller;
use App\Models\ClaseExtintor;
use App\Models\ControlExtintorCli;
use App\Models\MarcaExtintor;
use App\Models\TamanoExtintor;
use App\Services\AreaService;
use App\Services\ClienteLocalidadService;
use App\Services\ClienteService;
use App\Services\ExtintorService;
use App\Services\NomenclaturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\RepresentanteService;
use App\Services\EmailService;
use App\Helpers\AccesoHelper;
use App\Helpers\Utils;
use App\Imports\ControlExtintoresImport;

class 
ExtintorController extends Controller
{
    protected $extintorService, $nomenclaturaService, $areaService, $clienteService, $clienteLocalidadService;
    protected $representanteService;
    public function __construct(ExtintorService $extintorService, RepresentanteService $representanteService)
    {
        $this->extintorService = $extintorService;
        $this->nomenclaturaService = new NomenclaturaService;
        $this->areaService = new AreaService;
        $this->clienteService = new ClienteService;
        $this->clienteLocalidadService = new ClienteLocalidadService;
        $this->representanteService = $representanteService;
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

    public function obtener_control_extintor_por_cliente(Request $request, $id_cliente)
    {

        // Verifica si falta 'anio' o 'mes' (solo los que no están presentes)
        $params = $request->query();
        $debeRedirigir = false;

        if (!$request->has('anio')) {
            $params['anio'] = now()->year;
            $params['mes'] = now()->month;
            $debeRedirigir = true;
        }
    
        if ($debeRedirigir) {
            return redirect()->route('obtener_control_extintor_por_cliente', ['id_cliente' => $id_cliente] + $params);
        }

        $id_localidad = $request->query('localidad', 0);

        if (auth()->user()->hasRole('Cliente')) {
            if (!AccesoHelper::puedeAccederClienteYLocalidad($id_cliente, $id_localidad)) {
                abort(403, 'No tienes permiso para acceder a este cliente o localidad');
            }
        }

        try {

            $controlExtintores = $this->extintorService->get_control_extintor($id_cliente, $request);
            $ultimoControlExtintor = $this->extintorService->getUltimoControl($id_cliente, $request->query('localidad'));
            $areas = $this->areaService->get_areas_by_cliente($id_cliente);
            //$clases = $this->extintorService->getClases(['estado'=>1]);
            //$marcas = $this->extintorService->getMarcas(['estado'=>1]);
            //$pesos = $this->extintorService->getPesos(['estado'=>1]);
            $nomenclaturas = $this->nomenclaturaService->getNomenclaturasByTipo(['estado'=>1]);
            $clientes = $this->clienteService->obtenerClientesActivos($id_cliente);
            $localidad = $this->clienteLocalidadService->getLocalidadById($id_localidad);
            $totalExtintoresxLocalidad = $this->areaService->countSubareasByLocalidad($id_localidad);

            $nombreCliente = $clientes['0']->razon_social;
            $nombreLocalidad = $localidad->nombre;
            $emails = $this->representanteService->obtenerCorreosPorCliente($id_cliente);

            return view('Submenus.control_extintor', compact('controlExtintores', 'id_cliente','areas', 'nomenclaturas','clientes', 'ultimoControlExtintor', 'nombreCliente', 'nombreLocalidad', 'emails', 'totalExtintoresxLocalidad', 'id_localidad'));

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
                'imagen' => 'nullable|image|max:20480',
                'id_usuario' => 'nullable|exists:ci_users,id',
                'fecha_revision'       => ['required','date',
                    function ($attr, $value, $fail) use ($request) {
                        $exists = $this->extintorService->existsControlExtintor(
                            $request->input('localidad_control'),
                            $request->input('subarea_control'),
                            $value
                        );
                        if ($exists) {
                            $fail('Ya existe un control registrado para este extintor en el mes seleccionado.');
                        }
                    }
                ],
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
                'codigo_control' => 'required|string',
                'clase' => 'required|string',
                'marca' => 'required|string',
                'peso' => 'required|numeric',
                'unidad' => 'required|string',    
                'mango' => 'required|exists:nomenclatura,id',
                'mano' => 'required|exists:nomenclatura,id',
                'seguro' => 'required|exists:nomenclatura,id',
                'cilindro' => 'required|exists:nomenclatura,id',
                // Nullables: los controles antiguos no tienen estos datos y deben poder reguardarse sin completarlos
                'fecha_recarga' => 'nullable|date',
                'fecha_proxima_recarga' => ['nullable', 'date', function ($attr, $value, $fail) use ($request) {
                    $fechaRecarga = $request->input('fecha_recarga');
                    if ($fechaRecarga && $value && strtotime($value) < strtotime($fechaRecarga)) {
                        $fail('La fecha próxima de recarga debe ser posterior o igual a la fecha de recarga.');
                    }
                }],
                'fecha_fabricacion' => 'nullable|date',
                'fecha_prueba_hidrostatica' => 'nullable|date',
                'area_seguridad' => 'nullable|exists:nomenclatura,id',
                'instructivo' => 'nullable|exists:nomenclatura,id',
                'instructivo_clasificacion' => 'nullable|exists:nomenclatura,id',
                'flecha_ubicacion' => 'nullable|exists:nomenclatura,id',
                'silueta' => 'nullable|exists:nomenclatura,id',
                'rectangulo_instalacion' => 'nullable|exists:nomenclatura,id',
                'hidrante' => 'nullable|exists:nomenclatura,id',
                'dimension_vidrio' => 'nullable|string',
                'observaciones_marbete' => 'nullable|string',
                'observaciones' => 'nullable|string',
                'observaciones_generales' => 'nullable|array',
                'observaciones_generales.*' => 'exists:nomenclatura,id',
                'imagen' => 'nullable|image|max:20480', // Limitar a 2MB
                'fecha_revision'       => ['required','date',
                    /*function ($attr, $value, $fail) use ($request) {
                        $exists = $this->extintorService->existsControlExtintor(
                            $request->input('localidad_control'),
                            $request->input('subarea_control'),
                            $value
                        );
                        if ($exists) {
                            $fail('Ya existe un control registrado para este extintor en el mes seleccionado.');
                        }
                    }*/
                ],
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
        $empresa = $this->clienteService->getClienteById($request->id_cliente);
        $dataControlExtintores = $this->extintorService->get_control_extintor($request->id_cliente, $request, false);

        // lee columnas a mostrar (o un array vacío si no vienen)
        $selected = $request->input('columns', []);

        // Generar timestamp en formato YYYYMMDD_HHMMSS
        $timestamp = now()->format('Ymd_His');

        // Nombre dinámico: resumen_extintores_20250708_215530.xlsx
        $filename = "control_extintores_{$timestamp}.xlsx";

        // Pasar los datos a la exportación
        return Excel::download(
            new ControlExtintorExport($dataControlExtintores, $empresa, $selected),
            $filename);
    }


    public function sendReportEmail(Request $request, EmailService $emailService)
    {
        $emails = $request->input('emails', []); // lista de correos desde el frontend

        $empresa = $this->clienteService->getClienteById($request->id_cliente);
        $nombreCliente = $request->input('nombreCliente', '');
        $nombreLocalidad = $request->input('nombreLocalidad', '');
        $mes = (int) $request->input('mes', '');
        $nombreMes = Utils::nombreDelMes($mes);
        $anio = $request->input('anio', '');
        $fechaRevision = $nombreMes . ' ' . $anio;
        $dataControlExtintores = $this->extintorService->get_control_extintor($request->id_cliente, $request, false);

        // lee columnas a mostrar (o un array vacío si no vienen)
        $selected = $request->input('columns', []);
        // Generar y guardar el Excel en storage temporal
        $fileName = 'control_extintores_' . now()->format('Ymd_His') . '.xlsx';
        Excel::store(new ControlExtintorExport($dataControlExtintores, $empresa, $selected), $fileName, 'local');

        // Enviar el correo
        /*foreach ($emails as $email) {
            Mail::to($email)->send(new ControlExtintorReportMail($nombreCliente, $nombreLocalidad, $fileName));
        }*/
        $emailService->sendEmailWithAttachment(
            $emails,
            'Reporte de Control de Extintores',
            'emails.control_extintores',
            compact('nombreCliente', 'nombreLocalidad', 'fechaRevision'),
            $fileName
        );

        // Eliminar el archivo después de enviar (opcional)
        Storage::delete($fileName);

        return response()->json(['message' => 'Correo enviado correctamente']);
    }

    public function ubicacionesPorRevisar(Request $request, $clienteId)
    {
        $mes  = $request->query('mes');
        $anio = $request->query('anio');

        $service     = app(ExtintorService::class);
        $ubicaciones = $service->getSubareasDisponiblesByLocalidad($clienteId, $mes, $anio);

        return response()->json([
            'data'    => $ubicaciones,
            'cliente' => $clienteId,
            'mes'     => $mes,
            'anio'    => $anio,
        ]);
    }

    public function detalleExtintorExcel(Request $request, $cliente)
    {
        $fechaRevision = $request->query('fecha_revision');
        $fechaRecarga  = $request->query('fecha_prox_recarga');

        $service = app(ExtintorService::class);
        $rows = $service->getDetalleExcel($cliente, $fechaRevision, $fechaRecarga);
        $otrasNovedades = $service->getOtrasNovedades($cliente, $fechaRevision);
        $accesorios = $service->getAccesoriosExtintores($cliente, $fechaRevision);
        $novedadesGabinete = $service->getNovedadesGabinete($cliente, $fechaRevision);
        $novedadesEspeciales = $service->getNovedadesEspeciales($cliente, $fechaRevision);
        $observacionesInternas = $service->getObservacionesInternas($cliente, $fechaRevision);


        return response()->json([
            'cliente'              => $cliente,
            'fecha_revision'       => $fechaRevision,
            'fecha_prox_recarga'   => $fechaRecarga,
            'data'                 => $rows,
            'novedades'            => $otrasNovedades,
            'accesorios_extintor'  => $accesorios,
            'novedades_gabinete'   => $novedadesGabinete,
            'novedades_especiales' => $novedadesEspeciales,
            'observacionesInternas' => $observacionesInternas
        ]);
    }


    public function exportResumenExtintores(Request $request)
    {
        $id_localidad = $request->input('id_localidad');
        $empresa = $this->clienteService->getClienteByLocalidad($id_localidad);
        $localidad = $this->clienteLocalidadService->getLocalidadById($id_localidad);
        $fechaRevision = $request->input('fecha_revision');
        $fechaRecarga = $request->input('fecha_prox_recarga');

        $data = $this->extintorService->getDetalleExcel($id_localidad, $fechaRevision, $fechaRecarga);

        // Generar timestamp en formato YYYYMMDD_HHMMSS
        $timestamp = now()->format('Ymd_His');

        // Nombre dinámico: resumen_extintores_20250708_215530.xlsx
        $filename = "resumen_extintores_{$timestamp}.xlsx";

        return Excel::download(
            new ResumenExtintoresExport($data, $empresa, $localidad),
            $filename
        );
    }

    public function getUltimoControl(Request $request)
    {
        try {
            $idCliente = $request->query('cliente');
            $idLocalidad = $request->query('localidad');
            $codigo = $request->query('codigo');

            if (!$idCliente) {
                return response()->json(['error' => 'Cliente es requerido'], 400);
            }

            $control = $this->extintorService->getUltimoControl($idCliente, $idLocalidad, $codigo);
            return response()->json($control);
        } catch (\Exception $e) {
            //\Log::error('Error al obtener el último control extintor: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function obtenerImagen($id)
    {
        try {
            $imagen = $this->extintorService->obtenerImagenObservacion($id);

            if (!$imagen) {
                return response()->json(['error' => 'Imagen no encontrada'], 404);
            }

            return response()->json($imagen);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function handleImportControles(Request $request, ControlExtintoresImport $importer)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt,xlsx|max:20480',
        ]);

        $path = $request->file('archivo')->store('imports');

        try {
            Excel::import($importer, $path, 'local');

            $raw = $importer->errors();

            $counts = [
                '__omit_empty__'             => 0,
                '__omit_no_localidad__'      => 0,
                '__omit_auditor_not_found__' => 0,
                '__omit_no_area__'           => 0,
                '__omit_area_not_found__'    => 0,
                '__omit_no_subarea__'        => 0,
                '__omit_subarea_not_found__' => 0,
                '__omit_bad_dates__'         => 0,
            ];

            $others = [];
            foreach ($raw as $e) {
                if (isset($counts[$e])) {
                    $counts[$e]++;
                    continue;
                }

                if (str_starts_with($e, '__omit_area_not_found__')) {
                    $counts['__omit_area_not_found__']++;
                    continue;
                }
                if (str_starts_with($e, '__omit_subarea_not_found__')) {
                    $counts['__omit_subarea_not_found__']++;
                    continue;
                }
                $others[] = $e;
            }

            $warnings = [];
            if ($counts['__omit_empty__'] > 0)              $warnings[] = "Se omitieron {$counts['__omit_empty__']} filas vacías.";
            if ($counts['__omit_no_localidad__'] > 0)       $warnings[] = "Se omitieron {$counts['__omit_no_localidad__']} filas sin id_localidad.";
            if ($counts['__omit_auditor_not_found__'] > 0)  $warnings[] = "Se omitieron {$counts['__omit_auditor_not_found__']} filas por auditor no encontrado.";
            if ($counts['__omit_no_area__'] > 0)            $warnings[] = "Se omitieron {$counts['__omit_no_area__']} filas sin área.";
            if ($counts['__omit_area_not_found__'] > 0)     $warnings[] = "Se omitieron {$counts['__omit_area_not_found__']} filas por área no encontrada.";
            if ($counts['__omit_no_subarea__'] > 0)         $warnings[] = "Se omitieron {$counts['__omit_no_subarea__']} filas sin subárea.";
            if ($counts['__omit_subarea_not_found__'] > 0)  $warnings[] = "Se omitieron {$counts['__omit_subarea_not_found__']} filas por subárea no encontrada (filtrada por área).";
            if ($counts['__omit_bad_dates__'] > 0)          $warnings[] = "Se omitieron {$counts['__omit_bad_dates__']} filas con fechas inválidas.";

            $warnings = array_merge($warnings, $others);

            return response()->json([
                'status'   => empty($warnings) ? 'success' : 'warning',
                'message'  => empty($warnings) ? '¡Importación completada correctamente!' : 'Importación finalizada con advertencias.',
                'warnings' => $warnings,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al importar: '.$e->getMessage(),
            ], 500);
        }
    }

    
}
