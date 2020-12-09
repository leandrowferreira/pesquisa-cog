<?php

use App\User;
use App\Disciplina;
use App\Instituicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplinaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alunos  = [];
        $arquivo = database_path('seeds/dados/aulas.csv');
        if (!file_exists($arquivo)) {
            return false;
        }

        $userDiscs = file($arquivo);

        DB::beginTransaction();

        foreach ($userDiscs as $id => $userDisc) {
            if (!$id) {
                continue;
            }

            $userDisc = explode(';', trim($userDisc));

            $instTur = $userDisc[1] . str_replace(' ', '_', $userDisc[2]);
            $instId  = Instituicao::where('sigla', $userDisc[1])->first()->id;
            $discId  = Disciplina::where('nome', $userDisc[4])->first()->id;
            $profId  = User::where([['nome', $userDisc[3]], ['tipo', 'P']])->first()->id;

            if (!isset($alunos[$instTur])) {
                $alunos[$instTur] = User::where([['instituicao_id', $instId], ['turma', $userDisc[2]]])->get()->toArray();
            }

            foreach ($alunos[$instTur] as $aluno) {
                DB::table('disciplina_user')->insert([
                    'user_id'       => $aluno['id'],
                    'disciplina_id' => $discId,
                    'professor_id'  => $profId,
                ]);
            }
        }

        DB::commit();
    }
}
