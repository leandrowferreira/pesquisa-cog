<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(InstituicoesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(DisciplinasSeeder::class);
        $this->call(DisciplinaUserSeeder::class);
        // $this->call(DisciplinaProfessorSeeder::class);
        $this->call(PerguntasSeeder::class);
    }
}
