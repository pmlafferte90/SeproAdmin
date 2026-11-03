<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Elimina caché de permisos y roles para evitar errores
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Crear permisos ---

        $permisos = [
            'clientes.ver',
            'clientes.gestionar',
            'control-extintores.ver',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // --- Crear roles ---

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        // No se le asignan permisos porque el Gate::before le da acceso total

        $revisor = Role::firstOrCreate(['name' => 'Revisor']);
        $revisor->syncPermissions(['clientes.ver', 'clientes.gestionar']);

        $cliente = Role::firstOrCreate(['name' => 'Cliente']);
        $cliente->syncPermissions(['clientes.ver', 'control-extintores.ver']);
    }
}
