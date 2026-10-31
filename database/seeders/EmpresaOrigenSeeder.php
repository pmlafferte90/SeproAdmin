<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmpresaOrigen;

class EmpresaOrigenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar datos en la tabla empresa_origen
        EmpresaOrigen::insert([
            ['id' => 1, 'nombre' => 'SEPRO', 'color'=> '#ea2f2f','estado' => 1],
            ['id' => 2, 'nombre' => 'ECOLAFER', 'color'=> '#2fea51', 'estado' => 1],
        ]);
    }
}
