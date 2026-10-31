<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Models\Nomenclatura;
use App\Models\TipoNomenclatura;
use App\Services\NomenclaturaService;
use Illuminate\Http\Request;

class NomenclaturaController extends Controller
{
    protected $nomenclaturaService;

    public function __construct(NomenclaturaService $nomenclaturaService)
    {
        $this->nomenclaturaService = $nomenclaturaService;
    }
    public function index(Request $request)
    {
        $filtros = $request->all(); // Obtiene todos los parámetros del request

        $nomenclaturas = $this->nomenclaturaService->getNomenclaturas($filtros);
        $tipoNomenclatura = $this->nomenclaturaService->getTipoNomenclaturActivas();
        return view('Submenus.nomenclatura', compact('nomenclaturas','tipoNomenclatura'));
    }
    public function get_tipo_nomenclatura(Request $request)
    {
        $filtros = $request->all(); // Obtiene todos los parámetros del request

        $tipoNomenclaturas = $this->nomenclaturaService->getTipoNomenclatura($filtros);
        return view('Submenus.tipoNomenclatura', compact('tipoNomenclaturas'));
    }
    public function store(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'descripcion' => 'required|string|max:255',
                'id_tipo' => 'required|integer|exists:tipo,id', // Aseguramos que el área exista
                'abreviatura' => 'required|string|max:255',
            ]);

            // Llamar al servicio para crear un área
            $this->nomenclaturaService->crearNomenclatura([
                'descripcion' => $request->input('descripcion'),
                'id_tipo' => $request->input('id_tipo'), // Recogemos el id del select
                'abreviatura' => $request->input('abreviatura'),
                'estado' => 1,
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Nomenclatura creada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function crear_tipo_nomenclatura(Request $request)
    {
        // Validar y procesar datos
        try {
            // Validar los datos del request
            $request->validate([
                'descripcion' => 'required|string|max:255',
            ]);

            // Llamar al servicio para crear un área
            $this->nomenclaturaService->crearTipoNomenclatura([
                'descripcion' => $request->input('descripcion'),
                'estado' => 1,
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Tipo creado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'descripcion' => 'string|max:255',
                'tipoNomenclaturaId' => 'integer|exists:tipo,id', 
                'abreviatura' => 'string|max:255',

            ]);

            // Llamar al servicio para actualizar un área
            $this->nomenclaturaService->editarNomenclatura($id, [
                'descripcion' => $request->input('descripcion'),
                'id_tipo' => $request->input('id_tipo'), // Recogemos el id del select si está presente
                'abreviatura' => $request->input('abreviatura')
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Nomenclatura actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizar_tipo_nomenclatura(Request $request, $id)
    {
        try {
            $request->validate([
                'descripcion' => 'string|max:255'
            ]);

            // Llamar al servicio para actualizar un área
            $this->nomenclaturaService->editarTipoNomenclatura($id, [
                'descripcion' => $request->input('descripcion'),
            ]);

            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Tipo de Nomenclatura actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function cambiarEstado($id, Request $request)
    {
        try{
            $nomenclatura = Nomenclatura::find($id);

            if ($nomenclatura) {
                $nomenclatura->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                $nomenclatura->save(); // Guardar los cambios

                $mensaje = $nomenclatura->estado == 1 ? 'La nomenclatura ha sido restaurada exitosamente.' : 'La nomenclatura ha sido inactivada exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }

    public function cambiarEstado_tipo_nomenclatura($id, Request $request)
    {
        try{
            $tipo = TipoNomenclatura::find($id);

            if ($tipo) {
                $tipo->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                $tipo->save(); // Guardar los cambios

                if ($tipo->estado == 0) {
                    // Obtener las subáreas asociadas a esta área
                    $nomenclaturas = $this->nomenclaturaService->getNomenclaturas(['id_tipo'=>$tipo->id]);
                    
                    // Inactivar todas las subáreas relacionadas
                    foreach ($nomenclaturas as $nomenclatura) {
                        $nomenclatura->estado = 0; 
                        $nomenclatura->save(); 
                    }
                }

                $mensaje = $tipo->estado == 1 ? 'El tipo ha sido restaurado exitosamente.' : 'El tipo ha sido inactivado exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }

}
