<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Imports\RepresentantesImport;
use App\Models\Cliente;
use App\Models\Representante;
use App\Services\RepresentanteService;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelType;
use Spatie\Permission\Models\Role;

class RepresentanteController extends Controller
{

    public function __construct(private UsuarioService $UsuarioService, private RepresentanteService $representanteService)
    {

    }

    public function index(Request $request)
    {
        $representantes = $this->representanteService->obtenerRepresentantes($request);
        $clientes = Cliente::where('estado',1)->get();

        return view('Submenus.representante', compact('representantes','clientes'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            // Buscar dinámicamente el rol 'Cliente' usando Spatie
            $rolCliente = Role::where('name', 'Cliente')->firstOrFail();
            
            // Llamar al servicio para crear el usuario
            $request->merge(['rol_id' => $rolCliente->id]);
            $correos = $request->input('emails');
            $correoPrincipal = $correos[0] ?? null;
            $request->merge(['email' => $correoPrincipal]); // Asegurarse de que el correo esté en el request
            $usuario = $this->UsuarioService->crearUsuario($request);

            $id_user = $usuario->id; // ID del usuario creado

            // Llamar al servicio para crear el representante
            $responseRepresentante = $this->representanteService->crearRepresentante($request, $id_user);
            $representanteData = json_decode($responseRepresentante->getContent(), true);

            if (!$representanteData['ok']) {
                throw new \Exception($representanteData['error']);
            }

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Representante creado exitosamente',
                'data' => [
                    'usuario' => $usuario,
                    'representante' => $representanteData['data'],
                ],
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Si ocurre un error, deshacer la transacción
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al crear el representante',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $actualizar_representante = $this->representanteService->actualizarRepresentante($request, $id);
            $representanteData = json_decode($actualizar_representante->getContent(), true);

            if (!$representanteData['ok']) {
                throw new \Exception($representanteData['error']);
            }
            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Representante actualizado exitosamente.',
                'data' => $representanteData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Retornar el mensaje de error
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al actualizar el representante',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getRepresentanteById($id_representante)
    {
        try {
            $cliente = $this->representanteService->getRepresentanteById($id_representante);
            return response()->json([
                'success' => true,
                'data' => $cliente
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function cambiarEstado($id, Request $request)
    {
        try{
            $representante = Representante::find($id);

            if ($representante) {
                $representante->estado = $request->input('estado'); // Actualizar el estado según lo enviado
                if($request->input('estado') == 0){
                    $representante->fecha_fin = now();
                }
                $representante->save(); // Guardar los cambios
    
                $mensaje = $representante->estado == 1 ? 'El representante ha sido restaurado exitosamente.' : 'El representante ha sido inactivado exitosamente.';
                return response()->json([
                    'success' => true,
                    'message' => $mensaje
                ]);
            }       
        }catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } 
    }

    public function handleImport(Request $request, RepresentantesImport $importer)
    {
        // 2.1 Validación
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        // 2.2 Guarda el archivo en storage/app/imports
        $path = $request->file('archivo')->store('imports');

        try {
            // 2.3 Ejecuta la importación
            Excel::import(
                $importer,
                $path,
                'local',               // disco 'local' → storage/app
                ExcelType::XLSX             // si es XLSX: Excel::XLSX
            );

            return redirect()
                ->back()
                ->with('success', '¡Importación completada exitosamente!');

        } catch (\Exception $e) {
            // 2.4 Manejo de errores
            return redirect()
                ->back()
                ->withErrors("Error al importar: " . $e->getMessage());
        }
    }


}
