<?php

namespace App\Helpers;

class ArrayHelper
{
    /**
     * Elimina claves con valores vacíos ('', null). Conserva '0'. Hace trim() a strings.
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    public static function pruneEmpty(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            if (is_string($v)) $v = trim($v);
            if ($v === '' || $v === null) continue;
            $out[$k] = $v;
        }
        return $out;
    }

    /**
     * Considera la fila “vacía” si, tras hacer trim, todos los valores son '' o null.
     */
    public static function isRowEmpty(array $row): bool
    {
        foreach ($row as $v) {
            if (is_string($v)) $v = trim($v);
            if (!($v === '' || $v === null)) return false;
        }
        return true;
    }
}
