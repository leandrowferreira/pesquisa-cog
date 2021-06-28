<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerguntasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $linhas = file(database_path('seeds/dados/questionarios.txt'));

        $regs     = [];
        $idReg    = 0;
        $numLinha = 0;

        while ($numLinha < sizeof($linhas)) {
            $quest = trim($linhas[$numLinha++]);

            if ($quest) {
                if ($quest[0] == '*') {
                    $numP = 1;
                    $idReg++;
                    $nome = explode(':', substr($quest, 1));

                    $numPergs                    = $nome[1];
                    $regs[$idReg]['titulo']      = $nome[0];
                    $regs[$idReg]['numQuestoes'] = $numPergs[0];
                    $regs[$idReg]['perguntas']   = [];

                    $respostas = [];

                    while ($numPergs-- > 0) {
                        $regs[$idReg]['perguntas'][$numP]['enunciado'] = '';
                        while (!$regs[$idReg]['perguntas'][$numP]['enunciado']) {
                            $regs[$idReg]['perguntas'][$numP]['enunciado'] = trim($linhas[$numLinha++]);
                        }
                        for ($i = 0; $i < 3; $i++) {
                            $regs[$idReg]['perguntas'][$numP]['respostas'][] = trim($linhas[$numLinha++]);
                        }
                        $numP++;
                    }
                }
            }
        }

        foreach ($regs as $id => $reg) {
            $np = 1;
            foreach ($reg['perguntas'] as $pergunta) {
                DB::table('perguntas')->insert([
                    [
                        'numero'    => $np++, 'tipo' => 3, 'grupo' => $id == 1 ? 'EFEM' : 'EM3', 'feedback' => false,
                        'texto'     => $pergunta['enunciado'],
                        'resposta1' => $pergunta['respostas'][0],
                        'resposta2' => $pergunta['respostas'][1],
                        'resposta3' => $pergunta['respostas'][2]
                    ],
                ]);
            }
        }
    }
}
