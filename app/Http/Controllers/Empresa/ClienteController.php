<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Models\ClienteLocalidad;
use App\Models\EmpresaOrigen;
use App\Services\ClienteLocalidadService;
use App\Services\ClienteService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    protected $clienteService, $clienteLocalidadService;

    public function __construct()
    {
        $this->clienteService = new ClienteService;
        $this->clienteLocalidadService = new ClienteLocalidadService;
    }

    public function index(Request $request)
    {
        try {

            $clientes = $this->clienteService->obtenerClientes($request);
            $empresas = EmpresaOrigen::all();

            return view('Submenus.clientes', [
                'clientes' => $clientes,
                "empresas" => $empresas
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Crear cliente
            $responseCliente = $this->clienteService->crearCliente($request);
            $clienteData = json_decode($responseCliente->getContent(), true);

            if (!$clienteData['ok']) {
                throw new \Exception($clienteData['error']);
            }

            $cliente = $clienteData['data'];
            $id_cliente = $cliente['id'];

            // Verificar si existen localidades en la petición
            if (!empty($request->localidades)) {
                $localidadesArray = is_string($request->localidades) ? json_decode($request->localidades, true) : $request->localidades;
                $responseLocalidades = $this->clienteLocalidadService->crearLocalidades(
                    $id_cliente,
                    $localidadesArray,
                );
                $localidadesData = json_decode($responseLocalidades->getContent(), true);
                if (!$localidadesData['ok']) {
                    throw new \Exception($localidadesData['error']);
                }
            }

            // Si todo es correcto, hacer commit de la transacción
            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Cliente creado con éxito',
                'data' => [
                    'cliente' => $cliente,
                    'localidades' => $localidadesData['data'] ?? [],
                ],
            ], 201);
        } catch (\Exception $e) {
            // Si ocurre un error, hacer rollback de la transacción
            DB::rollBack();

            // Retornar el mensaje de error
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al crear el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $actualizar_cliente = $this->clienteService->actualizarCliente($request, $id);
            $clienteData = json_decode($actualizar_cliente->getContent(), true);

            if (!$clienteData['ok']) {
                throw new \Exception($clienteData['error']);
            }

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Cliente actualizado exitosamente.',
                'data' => $clienteData
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();

            // Retornar el mensaje de error
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al actualizar el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getClienteById($id_cliente)
    {
        try {
            $cliente = $this->clienteService->getClienteById($id_cliente);
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
        try {
            $cliente = $this->clienteService->cambiarEstadoCliente($id, $request->input('estado'));
            $mensaje = $cliente->estado == 1 ? 'El cliente ha sido restaurado exitosamente.' : 'El cliente ha sido inactivado exitosamente.';
            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function store_localidad_cliente(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validar los datos del formulario
            $validatedData = $request->validate([
                'id_cliente' => 'required|exists:cliente,id',
                'nombre_localidad' => 'required|unique:cliente_localidad,nombre|max:255',
                'direccion_localidad' => 'required|string|max:255',
                'ubicacion_localidad' => 'required|url',
            ], [
                'nombre_localidad.unique' => 'El nombre de localidad ingresado ya está registrado.',
            ]);

            $responseLocalidad = $this->clienteLocalidadService->crearLocalidad($validatedData);
            $data_localidad = json_decode($responseLocalidad->getContent(), true);

            if (!$data_localidad['ok']) {
                throw new \Exception($data_localidad['error']);
            }

            // Si todo es correcto, hacer commit de la transacción
            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Localidad creada con éxito',
                'data' => $data_localidad['data']
            ], 201);
        } catch (\Exception $e) {
            // Si ocurre un error, hacer rollback de la transacción
            DB::rollBack();

            // Retornar el mensaje de error
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al crear la localidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLocalidadById($id_localidad)
    {
        try {
            $localidad = $this->clienteLocalidadService->getLocalidadById($id_localidad);
            return response()->json([
                'success' => true,
                'data' => $localidad
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function update_localidad_cliente(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $actualizar_localidad = $this->clienteLocalidadService->actualizarLocalidad($request, $id);
            $localidadData = json_decode($actualizar_localidad->getContent(), true);

            if (!$localidadData['ok']) {
                throw new \Exception($localidadData['error']);
            }

            
            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Localidad actualizada exitosamente.',
                'data' => $localidadData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Retornar el mensaje de error
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al actualizar la Localidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLocalidadByIdCliente($id_cliente)
    {
        try {
            $localidades = $this->clienteLocalidadService->getLocalidadByIdCliente($id_cliente);

            $data_localidad = json_decode($localidades->getContent(), true);

            if (!$data_localidad['ok']) {
                throw new \Exception($data_localidad['error']);
            }

            return response()->json([
                'ok' => true, 
                'data' => $data_localidad['data']
            ]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function apiClientesActivos()
    {
        try {
            $clientes = $this->clienteService->obtenerClientesActivos();

            $resultado = collect($clientes)->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'razon_social' => $cliente->razon_social,
                ];
            });

            return response()->json(['data' => $resultado]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


}
