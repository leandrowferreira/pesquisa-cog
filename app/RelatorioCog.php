<?php

namespace App;

use TCPDF;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class RelatorioCog extends Model
{
    public static function geraPdf()
    {
        $cores = [
            ['FB', '1D', '11'], ['FB', '43', '11'], ['FB', '69', '11'], ['FB', '90', '11'], ['FB', 'B6', '11'], ['FB', 'DC', '11'],
            ['D5', 'D5', '11'], ['AF', 'CD', '11'], ['88', 'C6', '11'], ['62', 'BE', '11'], ['3C', 'B6', '11']
        ];

        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);

        $linha = 1000;
        $numQ  = 8;
        $alt   = 7;
        $lrg   = 233 / ($numQ + 1);

        $quant = [];
        foreach (Resposta::all() as $resposta) {
            if (!isset($quant[$resposta->professor_id])) {
                $quant[$resposta->professor_id] = [];
            }

            if (!isset($quant[$resposta->professor_id][$resposta->disciplina_id])) {
                $quant[$resposta->professor_id][$resposta->disciplina_id] = [];
            }

            $nPerg = $resposta->pergunta_id;
            if ($nPerg >= 8 && $nPerg <= 13) {
                $nPerg -= 7;
            } elseif ($nPerg >= 14) {
                $nPerg -= 7;
            }

            if (!isset($quant[$resposta->professor_id][$resposta->disciplina_id][$nPerg])) {
                $quant[$resposta->professor_id][$resposta->disciplina_id][$nPerg] = [0, 0, 0, 0];
            }

            $quant[$resposta->professor_id][$resposta->disciplina_id][$nPerg][0]++;
            $quant[$resposta->professor_id][$resposta->disciplina_id][$nPerg][$resposta->resposta]++;
        }

        uksort($quant, function ($a, $b) {
            return Str::ascii(User::find($a)->nome) > Str::ascii(User::find($b)->nome) ? 1 : -1;
        });

        foreach ($quant as $codP => $professor) {
            foreach ($professor as $codD => $disciplina) {
                if ($linha > 21) {
                    $pdf->AddPage('L', 'A4');
                    $pdf->Image(storage_path('app/logo_cog.png'), 15, 10, 69, 15, 'PNG');
                    $pdf->Image(storage_path('app/logo_sim10.png'), 245, 10, 41, 15, 'PNG');

                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->setxy(90, 10);
                    $pdf->cell(80, 6, 'AVALIAÇÃO ONLINE DOS PROFESSORES ' . date('Y'), 0, 0, 'L');

                    $pdf->SetLineStyle(['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]]);

                    //Cabeçalho da tabela

                    $pdf->setxy(11, 36);
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->cell(100, 6, 'Nome do professor');

                    $pdf->Rect(10, 36, 45 + $numQ * $lrg, 6);

                    for ($i = 1; $i <= $numQ + 1; $i++) {
                        $pdf->Rect(55 + $lrg * ($i - 1), 30, $lrg, 12);
                        if ($i <= $numQ) {
                            $pdf->line(55 + $lrg * ($i - 0.5), 36, 55 + $lrg * ($i - 0.5), 42);
                        }

                        $pdf->setxy(55 + $lrg * ($i - 1), 30 + ($i == 11 ? 3 : 0));
                        $pdf->SetFont('helvetica', 'B', 10);
                        $pdf->cell($lrg, 6, $i <= $numQ ? ('Questão ' . $i) : 'Média', 0, 0, 'C');

                        $pdf->SetFont('helvetica', 'B', 8);
                        if ($i <= $numQ) {
                            $pdf->setxy(55 + $lrg * ($i - 1), 36);
                            $pdf->cell($lrg / 2, 6, 'Quant', 0, 0, 'C');
                            $pdf->setxy(55 + $lrg * ($i - 1 + 0.5), 36);
                            $pdf->cell($lrg / 2, 6, 'Nota', 0, 0, 'C');
                        } else {
                        }
                    }
                    $linha = 0;
                }

                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->setxy(11, 41.5 + $linha * $alt);
                $pdf->cell(44, 6, User::find($codP)->nome, 0, 0, 'L');

                $pdf->SetFont('helvetica', '', 7);
                $pdf->setxy(11, 44 + $linha * $alt);
                $pdf->cell(44, 6, mb_convert_case(Disciplina::find($codD)->nome, MB_CASE_TITLE, 'UTF-8'), 0, 0, 'L');
                $nota  = 0;
                $quant = 0;

                for ($nq = 1; $nq <= $numQ; $nq++) {
                    if (isset($disciplina[$nq]) && $disciplina[$nq][0]) {
                        $nt = (10 * $disciplina[$nq][1] + 6.6 * $disciplina[$nq][2] + 3.3 * $disciplina[$nq][3]) / $disciplina[$nq][0];
                        $r  = hexdec($cores[intval($nt)][0]);
                        $g  = hexdec($cores[intval($nt)][1]);
                        $b  = hexdec($cores[intval($nt)][2]);
                    } else {
                        $nt = '-';
                        $r  = $g  = $b  = 192;
                    }

                    $st = ['width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,10', 'color' => [$r, $g, $b]];

                    $pdf->Rect(55 + $lrg * ($nq - 1), 42 + $linha * $alt + 0.1, $lrg, $alt, 'F', $st, [$r, $g, $b]);

                    if (isset($disciplina[$nq]) && $disciplina[$nq][0]) {
                        $pdf->SetFont('helvetica', '', 9);
                        $pdf->setxy(55 + $lrg * ($nq - 1), 42 + $linha * $alt);
                        $pdf->cell($lrg / 4, 6, $disciplina[$nq][0], 0, 0, 'C');

                        $pdf->SetFont('helvetica', '', 6);
                        $pdf->setxy(53 + $lrg * ($nq - 1 + 0.25), 42.0 + $linha * $alt);
                        $pdf->cell($lrg / 4, 2, $disciplina[$nq][1], 0, 0, 'C');
                        $pdf->setxy(54.5 + $lrg * ($nq - 1 + 0.25), 43.7 + $linha * $alt);
                        $pdf->cell($lrg / 4, 2, $disciplina[$nq][2], 0, 0, 'C');
                        $pdf->setxy(56 + $lrg * ($nq - 1 + 0.25), 45.4 + $linha * $alt);
                        $pdf->cell($lrg / 4, 2, $disciplina[$nq][3], 0, 0, 'C');
                    }
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->setxy(55 + $lrg * ($nq - 0.5), 42 + $linha * $alt);
                    if ($nt == '-') {
                        $pdf->cell($lrg / 2, 6, $nt, 0, 0, 'C');
                    } else {
                        $pdf->cell($lrg / 2, 6, number_format($nt, 1), 0, 0, 'C');
                    }
                    if ($nt != '-') {
                        $nota += $nt;
                        $quant++;
                    }
                }

                if ($quant) {
                    $r = hexdec($cores[intval($nota / $quant)][0]);
                    $g = hexdec($cores[intval($nota / $quant)][1]);
                    $b = hexdec($cores[intval($nota / $quant)][2]);
                    $pdf->Rect(55 + $lrg * $numQ, 42 + $linha * $alt + 0.1, $lrg, $alt, 'DF', $st, [$r, $g, $b]);
                    $pdf->setxy(55 + $lrg * $numQ, 42 + $linha * $alt);
                    $pdf->SetFont('helvetica', 'B', 11);
                    $pdf->cell($lrg, 6, number_format($nota / $quant, 2), 0, 0, 'C');
                    // $pdf->Line(55 + $lrg * 7, 42 + ($linha + 1) * $alt, 55 + $lrg * 11, 42 + ($linha + 1) * $alt);
                }

                $pdf->Rect(10, 42 + $linha * $alt, 45 + ($numQ + 1) * $lrg, $alt);

                for ($i = 1; $i <= 12; $i++) {
                    $pdf->Line(55 + $lrg * ($i - 1), 42 + $linha * $alt, 55 + $lrg * ($i - 1), 42 + ($linha + 1) * $alt);
                }

                $linha++;
            }
        }

        $pdf->Output(storage_path('app/Resultado Pesquisa Online ' . date('Y') . '.pdf'), 'F');
    }
}
