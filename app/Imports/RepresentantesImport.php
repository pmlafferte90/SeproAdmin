<?php

namespace App\Imports;

use App\Models\CiUser;
use App\Models\Representante;
use App\Services\UsuarioService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class RepresentantesImport implements ToModel, WithHeadingRow, WithChunkReading
{

    public function __construct(protected UsuarioService $usuarioService)
    {
    }

    public function model(array $row)
    {
        $auditorId = auth()->id() ?? 1; // Asigna un ID de auditor por defecto si no hay usuario autenticado

        return DB::transaction(function() use ($row, $auditorId) {

            $id_cliente = $row['id_cliente'];
            $nombre = $row['nombre'];
            $apellido = $row['apellido'];
                        // 1. Extrae y guarda todos los correos
            $correos = array_filter(
                array_map('trim', explode(',', $row['correo'] ?? ''))
            );
            $correoPrincipal = $correos[0] ?? null;

            $baseUsername = $this->usuarioService->getBaseUsername($nombre, $apellido);
            $username = $this->usuarioService->obtenerUsernameUnico($baseUsername);
            $plainPassword = 'Sepro2025';
            $rolCliente = Role::where('name', 'Cliente')->firstOrFail();

            $usuario = CiUser::create([
                'id_cliente'     => $id_cliente,
                'usuario'        => $username,
                'password'       => bcrypt($plainPassword),
                'rol'            => $rolCliente->id,
                'nombre'         => $nombre,
                'apellido'       => $apellido,
                'email'          => $correoPrincipal,
                'estado'         => 1,
                'fecha_creacion' => now(),
                'id_usuario'     => $auditorId,
            ]);

            $usuario->syncRoles([$rolCliente->name]);

            $verTodasLocalidades = isset($row['ver_todas_localidades'])
                && intval($row['ver_todas_localidades']) === 1;

            $representante = Representante::create([
                'id_cliente' => $id_cliente,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'id_usuario' => $usuario->id,
                'identificacion' => $row['identificacion'],
                'telefono' => $row['telefono'],
                'correo' => $correoPrincipal,
                'estado' => 1,
                'fecha_creacion' => now(),
                'id_usuario_auditor' => $auditorId,
                'ver_todas_localidades' => $verTodasLocalidades,
            ]);

            foreach ($correos as $idx => $mail) {
                $representante->emails()->create([
                    'email'     => $mail,
                    'principal' => $idx === 0,
                ]);
            }

            if (! empty($emails)) {
                $representante->update([ 'correo' => $correoPrincipal ]);
            }


            if (! $verTodasLocalidades) {
                // parseamos IDs de localidades (puede venir "1,2,4")
                $locIds = array_filter(
                    array_map('trim', explode(',', $row['id_localidad'] ?? ''))
                );
                if (! empty($locIds)) {
                    $representante->localidades()->sync($locIds);
                }
            }

            //(Opcional) Envía email con credenciales
            // Mail::to($rep->correo)
            //     ->send(new CredentialsMail($username, $plainPassword));

            return $representante;
        });
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
