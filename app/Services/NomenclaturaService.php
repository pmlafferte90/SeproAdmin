<?php

namespace App\Services;

use App\Models\Area;
use App\Models\AreaEmpresa;
use App\Models\Nomenclatura;
use App\Models\SubArea;
use App\Models\TipoNomenclatura;
use Exception;
use Illuminate\Support\Facades\Log;

class NomenclaturaService
{
    public function getNomenclaturas($filtros)
    {
        try {
            $query = Nomenclatura::with('tipoNomenclatura');

            if (isset($filtros['id_tipo'])) {
                $query->where('id_tipo', $filtros['id_tipo']);
            }
            if(isset($filtros['estado'])){
                $query->where('nomenclatura.estado', $filtros['estado']);            
            }
            if (isset($filtros['buscar'])) {
                $query->where(function ($q) use ($filtros) {
                    $q->where('nomenclatura.descripcion', 'like', "%{$filtros['buscar']}%")
                      ->orWhere('nomenclatura.abreviatura', 'like', "%{$filtros['buscar']}%");                });
            }
            // Comprobar si se requiere paginación
            $perPage = isset($filtros['perPage']) ? $filtros['perPage'] : 5; // Definir un valor predeterminado
            return $query->paginate($perPage); // Paginación

        } catch (Exception $e) {
            throw new Exception('Error al crear el área: ' . $e->getMessage());
        }
    }

    public function getTipoNomenclaturActivas()
    {
        try {
            $query = TipoNomenclatura::select(
                'id',
                'descripcion',
                'estado'
            );

            $query->where('estado', 1);

            return $query->get();

        } catch (Exception $e) {
            throw new Exception('Error al crear el área: ' . $e->getMessage());
        }
    }

    public function getTipoNomenclatura($filtros)
    {
        try {
            $query = TipoNomenclatura::select(
                'id',
                'descripcion',
                'estado'
            );

            if (isset($filtros['estado'])) {
                $query->where('estado', $filtros['estado']);
            }
            if (isset($filtros['buscar'])) {
                $query->where(function ($q) use ($filtros) {
                    $q->where('tipo.descripcion', 'like', "%{$filtros['buscar']}%");
                });
            }
            // Comprobar si se requiere paginación
            $perPage = isset($filtros['perPage']) ? $filtros['perPage'] : 5; // Definir un valor predeterminado
            return $query->paginate($perPage); // Paginación

        } catch (Exception $e) {
            throw new Exception('Error al crear el área: ' . $e->getMessage());
        }
    }

    public function getNomenclaturasByTipo($filtros)
    {
        try {
            $query = TipoNomenclatura::with(['nomenclaturas' => function ($q) {
                // Filtrar solo nomenclaturas activas (estado = 1)
                $q->where('estado', 1);
            }])
            ->select('id', 'descripcion', 'estado');
            
            if (isset($filtros['estado'])) {
                $query->where('estado', $filtros['estado']);
            }
            if (isset($filtros['id_tipo'])) {
                $query->where('tipo.id', $filtros['id_tipo']);
            }

            return $query->get();

        } catch (Exception $e) {
            throw new Exception('Error al obtener nomenclaturas por tipo: ' . $e->getMessage());
        }
    }


    public function crearNomenclatura(array $data)
    {
        try {
            return Nomenclatura::create($data);
        } catch (Exception $e) {
            throw new Exception('Error al crear la nomenclatura: ' . $e->getMessage());
        }
    }

    public function editarNomenclatura($id, array $data)
    {
        try {
            $nomenclatura = Nomenclatura::findOrFail($id); 
            $nomenclatura->update($data);
            return $nomenclatura;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar la nomenclatura: ' . $e->getMessage());
        }
    }
    public function crearTipoNomenclatura(array $data)
    {
        try {
            return TipoNomenclatura::create($data);
        } catch (Exception $e) {
            throw new Exception('Error al crear el tipo de nomenclatura: ' . $e->getMessage());
        }
    }

    public function editarTipoNomenclatura($id, array $data)
    {
        try {
            $tipoNomenclatura = TipoNomenclatura::findOrFail($id); 
            $tipoNomenclatura->update($data);


            return $tipoNomenclatura;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar el área: ' . $e->getMessage());
        }
    }
}
