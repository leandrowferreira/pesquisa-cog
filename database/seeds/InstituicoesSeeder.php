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
            ['sigla' => 'BV', 'nome' => 'Boa Viagem'],
            ['sigla' => 'CF', 'nome' => 'Casa Forte'],
        ]);
    }
}
