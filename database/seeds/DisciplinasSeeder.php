<?php

use App\Instituicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arquivo = database_path('seeds/dados/disciplinas.csv');
        if (!file_exists($arquivo)) {
            return false;
        }

        $disciplinas = file($arquivo);

        DB::beginTransaction();

        foreach ($disciplinas as $id => $disciplina) {
            if (!$id) {
                continue;
            }

            $disciplina = explode(';', $disciplina);

            DB::table('disciplinas')->insert([
                'id'   => trim($disciplina[0]),
                'nome' => trim($disciplina[1]),
            ]);
        }

        DB::commit();
    }
}
