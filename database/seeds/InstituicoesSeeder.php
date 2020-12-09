<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstituicoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('instituicoes')->insert([
            ['sigla' => 'bv', 'nome' => 'Boa Viagem'],
            ['sigla' => 'cf', 'nome' => 'Casa Forte'],
        ]);
    }
}
