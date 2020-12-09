<?php

use App\User;
use App\Disciplina;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplinaProfessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arquivo = database_path('seeds/dados/disciplina_professor.csv');
        if (!file_exists($arquivo)) {
            return false;
        }

        $profDiscs = file($arquivo);

        DB::beginTransaction();

        foreach ($profDiscs as $profDisc) {
            $profDisc = explode(';', $profDisc);

            $professor  = User::where('md5', trim($profDisc[1]))->first();
            $disciplina = Disciplina::where('codigo', trim($profDisc[0]))->firstOrFail();

            // echo $professor->nome . ' - ' . $disciplina->nome . "\n";

            DB::table('disciplina_professor')->insert([
                'user_id'       => $professor->id,
                'disciplina_id' => $disciplina->id,
            ]);
        }

        DB::commit();
    }
}
