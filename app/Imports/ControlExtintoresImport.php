<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\SubArea;
use App\Models\Tipo;
use App\Models\Nomenclatura;
use App\Models\ControlExtintorCli;
use App\Models\TipoNomenclatura;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Helpers\ArrayHelper;

class ControlExtintoresImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /** @var array<string,mixed> */
    protected array $ctx = [];

    /** @var string[] */
    protected array $importErrors = [];

    public function setContext(array $ctx): self
    {
        $this->ctx = $ctx;
        return $this;
    }

    /** @return string[] */
    public function errors(): array
    {
        return $this->importErrors;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_LOWER);
        $row = ArrayHelper::pruneEmpty($row);

        if (empty($row) || ArrayHelper::isRowEmpty($row)) {
            $this->importErrors[] = '__omit_empty__';
            return null;
        }

        try {
            return DB::transaction(function () use ($row) {

                // ---- id_localidad (soft omit si falta) ----
                $idLocalidad = $row['id_localidad'] ?? Arr::get($this->ctx, 'id_localidad');
                if (empty($idLocalidad)) {
                    $this->importErrors[] = '__omit_no_localidad__';
                    return null;
                }

                // ---- Auditor (username -> id) (soft omit si no existe) ----
                $auditorId = auth()->id() ?? 1;
                if (!empty($row['usuario_auditor'])) {
                    $aud = DB::table('ci_users')->where('usuario', trim($row['usuario_auditor']))->first();
                    if (!$aud) {
                        $this->importErrors[] = '__omit_auditor_not_found__';
                        return null;
                    }
                    $auditorId = (int)$aud->id;
                }

                // ---- Área / Subárea (soft omit si faltan o no se encuentran) ----
                $areaName = trim((string)($row['area'] ?? $row['area_nombre'] ?? ''));
                if ($areaName === '') {
                    $this->importErrors[] = '__omit_no_area__';
                    return null;
                }
                $area = Area::where('nombre', 'like', "%{$areaName}%")->first();
                if (!$area) {
                    $this->importErrors[] = '__omit_area_not_found__:'.$areaName;
                    return null;
                }

                $subareaName = trim((string)($row['ubicacion'] ?? $row['subarea'] ?? $row['sub_area'] ?? ''));
                if ($subareaName === '') {
                    $this->importErrors[] = '__omit_no_subarea__';
                    return null;
                }
                $subarea = SubArea::where('id_area', $area->id)
                    ->where('nombre', 'like', "%{$subareaName}%")
                    ->first();
                if (!$subarea) {
                    $this->importErrors[] = '__omit_subarea_not_found__:'.$area->id.':'.$subareaName;
                    return null;
                }

                // ---- Fechas (soft omit si inválidas) ----
                $fechaRevision    = $this->parseDate($row['fecha_revision'] ?? null, true);
                $fechaRecarga     = $this->parseYearMonth($row['fecha_recarga'] ?? null);
                $fechaProxRecarga = $this->parseYearMonth($row['fecha_prox_recarga'] ?? $row['fecha_proxima_recarga'] ?? null);

                if (!$fechaRevision || !$fechaRecarga || !$fechaProxRecarga) {
                    $this->importErrors[] = '__omit_bad_dates__';
                    return null;
                }

                // ---- Datos y nomenclaturas (igual que ya tienes) ----
                $code   = trim((string)($row['codigo'] ?? ''));
                $class  = trim((string)($row['clase'] ?? ''));
                $brand  = trim((string)($row['marca'] ?? ''));
                $weight = $this->toDecimal($row['peso'] ?? null);
                $unit   = trim((string)($row['unidad'] ?? ''));

                $fechaFabricacion        = $this->parseDate($row['fecha_fabricacion'] ?? null);
                $fechaPruebaHidrostatica = $this->parseDate($row['fecha_prueba_hidrostatica'] ?? null);

                $glassDimension = trim((string)($row['dimension_vidrio'] ?? ''));
                $marbeteNote    = trim((string)($row['marbete'] ?? $row['novedad_marbete'] ?? ''));
                $internalNotes  = trim((string)($row['observaciones_internas'] ?? $row['observaciones_db'] ?? ''));

                $mango         = $this->resolveNomen('MANGUERA',                  $row['manguera'] ?? $row['mango'] ?? null);
                $mano          = $this->resolveNomen('MANOMETRO',                 $row['manometro'] ?? $row['mano'] ?? null);
                $seguro        = $this->resolveNomen('SEGURO',                    $row['seguro'] ?? null);
                $cilindro      = $this->resolveNomen('CILINDRO',                  $row['cilindro'] ?? null);
                $areaSeguridad = $this->resolveNomen('AREA-SEGURIDAD',            $row['area_seguridad'] ?? null);
                $instUso       = $this->resolveNomen('INSTRUCTIVO-USO',           $row['instructivo_uso'] ?? $row['instructivo'] ?? null);
                $instClasif    = $this->resolveNomen('INSTRUCTIVO-CLASIFICACIÓN', $row['instructivo_clasificacion'] ?? null);
                $flecha        = $this->resolveNomen('FLECHA-UBICACIÓN',          $row['flecha_ubicacion'] ?? null);
                $silueta       = $this->resolveNomen('SILUETA-EXTINTOR',          $row['silueta_extintor'] ?? $row['silueta'] ?? null);
                $rectangulo    = $this->resolveNomen('RECTANGULO',                $row['rectangulo_instalacion'] ?? null);
                $hidrante      = $this->resolveNomen('HIDRANTE',                  $row['extintor_hidrante'] ?? $row['hidrante'] ?? null);

                $control = ControlExtintorCli::create([
                    'id_cliente_localidad'       => $idLocalidad,
                    'id_subarea_empresa'         => $subarea->id,
                    'fecha_revision'             => $fechaRevision,
                    'codigo'                     => $code ?: null,
                    'clase'                      => $class ?: null,
                    'marca'                      => $brand ?: null,
                    'peso'                       => $weight ?: null,
                    'unidad'                     => $unit ?: null,
                    'mango'                      => $mango ?: null,
                    'mano'                       => $mano ?: null,
                    'seguro'                     => $seguro ?: null,
                    'cilindro'                   => $cilindro ?: null,
                    'fecha_recarga'              => $fechaRecarga,
                    'fecha_prox_recarga'         => $fechaProxRecarga,
                    'fecha_fabricacion'          => $fechaFabricacion ?: null,
                    'fecha_prueba_hidrostatica'  => $fechaPruebaHidrostatica ?: null,
                    'area_seguridad'             => $areaSeguridad ?: null,
                    'instructivo_uso'            => $instUso ?: null,
                    'instructivo_clasificacion'  => $instClasif ?: null,
                    'flecha_ubicacion'           => $flecha ?: null,
                    'silueta'                    => $silueta ?: null,
                    'hidrante'                   => $hidrante ?: null,
                    'dimension_vidrio'           => $glassDimension ?: null,
                    'rectangulo_instalacion'     => $rectangulo ?: null,
                    'novedad_marbete'            => $marbeteNote ?: null,
                    'observaciones'              => $internalNotes ?: null,
                    'imagen_observacion'         => null,
                    'id_usuario'                 => $auditorId,
                    'estado'                     => 1,
                    'fecha_creacion'             => now(),
                    // 'id_usuario_auditor'      => $auditorId, // si lo quieres guardar, descomenta
                ]);

                if (!empty($row['observaciones'])) {
                    $items  = array_filter(array_map('trim', explode(',', (string)$row['observaciones'])));
                    $obsIds = [];
                    foreach ($items as $val) {
                        $id = $this->resolveNomen('MARBETE', $val, true);
                        if ($id) $obsIds[] = $id;
                    }
                    $control->observacionesNomenclatura()->sync($obsIds);
                }

                return $control;
            });
        } catch (\Throwable $e) {
            $ref = $row['codigo'] ?? ($row['ubicacion'] ?? 'fila_sin_codigo');
            $this->importErrors[] = "[$ref] " . $e->getMessage();
            return null;
        }
    }

    // ==================== Helpers ====================

    protected function toDecimal($value): ?string
    {
        if ($value === null || $value === '') return null;
        $num = str_replace(',', '.', trim((string)$value));
        return is_numeric($num) ? number_format((float)$num, 2, '.', '') : null;
    }

    protected function parseDate($value, bool $required = false): ?string
    {
        if ($value === null || $value === '') return $required ? null : null;

        $value = trim((string)$value);
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }
        if (is_numeric($value)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
                return Carbon::instance($dt)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }
        return $required ? null : null;
    }

    protected function parseYearMonth($value): ?string
    {
        if ($value === null || $value === '') return null;

        $value = trim((string)$value);
        $formats = ['Y-m', 'm/Y', 'm-Y', 'Y/m', 'F Y', 'M Y'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->startOfMonth()->format('Y-m-d');
            } catch (\Throwable $e) {}
        }
        if (is_numeric($value)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
                return Carbon::instance($dt)->startOfMonth()->format('Y-m-d');
            } catch (\Throwable $e) {}
        }
        return null;
    }

    /**
     * Resuelve id de nomenclatura por Tipo.descripcion y valor (abreviatura o descripción).
     * Devuelve 0 si vacío o "OK" (si no hay "OK" explícito). Si $strict=true y no hay match → lanza excepción.
     */
    protected function resolveNomen(string $tipoDescripcion, $valor, bool $strict = false): ?int
    {
        $valor = trim((string)($valor ?? ''));
        if ($valor === '') return null;

        $tipo = TipoNomenclatura::where('descripcion', $tipoDescripcion)->first();
        if (!$tipo) {
            if ($strict) throw new \RuntimeException("Tipo no encontrado: {$tipoDescripcion}");
            return null;
        }

        if (mb_strtoupper($valor) === 'OK') {
            $okId = Nomenclatura::where('id_tipo', $tipo->id)
                ->where(function ($q) {
                    $q->where('abreviatura', 'OK')->orWhere('descripcion', 'OK');
                })
                ->value('id');
            return $okId ? (int)$okId : null;
        }

        $nom = Nomenclatura::where('id_tipo', $tipo->id)
            ->where(function ($q) use ($valor) {
                $q->where('abreviatura', $valor)
                ->orWhere('descripcion', 'like', "%{$valor}%");
            })
            ->first();

        if (!$nom) {
            if ($strict) throw new \RuntimeException("Nomenclatura no encontrada para tipo {$tipoDescripcion}: {$valor}");
            return null;
        }

        return (int) $nom->id;
    }
}
