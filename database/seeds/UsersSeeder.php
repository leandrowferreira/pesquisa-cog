<?php

use App\Instituicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $users;
    protected $profs;

    protected $cam_prof_nao_encontrado;

    public function run()
    {
        $arqAlunos  = database_path('seeds/dados/alunos.csv');
        $arqProfs   = database_path('seeds/dados/professores.csv');

        DB::beginTransaction();

        $this->users = file($arqProfs);
        foreach ($this->users as $id => $user) {
            if (!$id) {
                continue;
            }

            $user = explode(';', trim($user));

            DB::table('users')->insert([
                'id'             => trim($user[0]),
                'nome'           => trim($user[1]),
                'matricula'      => null,
                'turma'          => null,
                'grupo'          => null,
                'tipo'           => 'P',

                'instituicao_id' => null,
                'md5'            => md5(trim($user[1])),
            ]);
        }

        $this->users = file($arqAlunos);
        foreach ($this->users as $id => $user) {
            if (!$id) {
                continue;
            }

            $user = explode(';', trim($user));

            DB::table('users')->insert([
                'nome'           => trim($user[3]),
                'matricula'      => trim($user[2]),
                'turma'          => trim($user[4]),
                'grupo'          => trim($user[1]),
                'tipo'           => 'A',

                'instituicao_id' => Instituicao::where('sigla', $user[0])->first()->id,
                'md5'            => md5(trim($user[2])),
            ]);
        }

        DB::commit();
    }
}
