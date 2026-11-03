<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AccesoHelper
{
    public static function puedeAccederClienteYLocalidad($idCliente, $idLocalidad = null): bool
    {
        $user = Auth::user();

        // Admin siempre puede
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Validar que el cliente asignado coincida
        if ($user->id_cliente != $idCliente) {
            return false;
        }

        $representante = $user->representante;

        // Si no hay representante, denegar
        if (!$representante) {
            return false;
        }

        // Si puede ver todas las localidades, permitir
        if ($representante->ver_todas_localidades) {
            return true;
        }

        // Si no se especifica localidad, denegar
        if (!$idLocalidad) {
            return false;
        }

        // Validar si el representante tiene acceso a esa localidad
        return $representante->localidades->contains('id', $idLocalidad);
    }
}
