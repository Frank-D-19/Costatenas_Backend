<?php

use Illuminate\Database\Seeder;
use Agencia\Unidad_Medida;
use Agencia\Parametro;
use Agencia\Indicador;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Unidad_Medida::create(['nombre' => 'unidades']);
        Unidad_Medida::create(['nombre' => '%']);
        Unidad_Medida::create(['nombre' => 'dS/m']);
    }
}
