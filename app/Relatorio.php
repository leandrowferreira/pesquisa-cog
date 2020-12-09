<?php

namespace App;

use TCPDF;
use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    protected $table = null;

    public $pagina;
    public $pdf;
    public $disciplina;
    public $linha1;
    public $paginasDisciplina;

    public $cores = [
        ['FB', '1D', '11'], ['FB', '43', '11'], ['FB', '69', '11'], ['FB', '90', '11'], ['FB', 'B6', '11'], ['FB', 'DC', '11'],
        ['D5', 'D5', '11'], ['AF', 'CD', '11'], ['88', 'C6', '11'], ['62', 'BE', '11'], ['3C', 'B6', '11']
    ];

    public function __construct($did = null)
    {
        $this->pdf = new TCPDF();

        $this->pdf->SetAuthor('Leandro Ferreira');
        $this->pdf->SetTitle('Relatório da pesquisa');
        $this->pdf->SetSubject('Avaliação de Prática Pedagógica Discente');
        $this->pdf->SetKeywords('FCAP, POLI, UPE, Sim10');

        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetAutoPageBreak(false);

        $this->pagina = 1;

        // TESTE
        if ($did) {
            $this->adiciona(Disciplina::find($did));
            $this->salva();
        }

        return $this;
    }

    public function monta()
    {
        foreach (Instituicao::get() as $instituicao) {
            foreach ($instituicao->disciplinas()->orderBy('nome')->orderBy('codigo')->get() as $disciplina) {
                if ($disciplina->respostas()->count()) {
                    dump($instituicao->sigla . ' - ' . $disciplina->nome . ' - ' . $disciplina->respostas()->count());
                    $this->adiciona($disciplina);
                }
            }
        }
        $this->salva();
    }

    public function novaPagina($titulo = null)
    {
        $this->pdf->AddPage();

        //Numeração
        $this->pdf->SetXY(10, 285);
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->SetTextColor(128);
        $this->pdf->SetFillColor(255);
        $this->pdf->MultiCell(200, 0, $this->pagina++, 0, 0, 'C');

        //Título
        $this->pdf->SetXY(50, 14);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(150, 0, 'AVALIAÇÃO DE PRÁTICA PEDAGÓGICA DISCENTE', 0, 0, 'C');

        $this->pdf->SetXY(50, 19);
        $this->pdf->SetFont('helvetica', '', 11);
        $this->pdf->Cell(150, 0, 'Semestre letivo 2018.2', 0, 0, 'C');

        //Retângulos do cabeçalho
        $this->pdf->SetLineStyle($this->linha1);
        $this->pdf->SetFillColor(229, 230, 231);
        $this->pdf->RoundedRect(10, 32, 110, 30, 2, '1111', 'DF');
        $this->pdf->RoundedRect(123, 32, 77, 30, 2, '1111', 'DF');

        //Informações sobre a disciplina
        $this->pdf->SetXY(11, 33);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont('helvetica', 'B', 13);
        $this->pdf->Cell(0, 0, $this->disciplina->codigo);

        $this->pdf->SetXY(11, 39);
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(100, 0, $this->disciplina->nome);

        $w = 0;
        $this->pdf->SetFont('helvetica', '', 10);
        foreach ($this->disciplina->professores as $professor) {
            $this->pdf->SetXY(11, 45 + 5 * $w++);
            $this->pdf->Cell(100, 0, $professor->nome);
        }

        //Informações sobre a "representatividade"
        $qResp = $this->disciplina->respostas->where('pergunta_id', 1)->where('tipo', 'A')->count();
        $qMatr = $this->disciplina->users()->where('tipo', 'A')->count();

        $this->pdf->SetXY(124, 33);
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->Cell(77, 0, 'Representatividade');

        $this->pdf->SetXY(124, 55);
        $this->pdf->SetFont('helvetica', 'B', 12);
        $this->pdf->Cell(77, 0, $qResp . ' respondentes / ' . $qMatr . ' matriculados');

        $this->pdf->SetXY(124, 36);
        $this->pdf->SetTextColor(128);
        $this->pdf->SetFont('helvetica', 'B', 45);
        $this->pdf->MultiCell(74, 0, number_format($qResp / $qMatr * 100, 1, ',', '.') . '%', 0, 'C');

        //Título da parte do relatório
        $this->pdf->SetXY(9.5, 72);
        $this->pdf->SetTextColor(149, 150, 153);
        $this->pdf->SetFont('helvetica', '', 18);
        $this->pdf->Cell(70, 0, $titulo);

        $this->pdf->Line(10, 80, 200, 80, ['width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [149, 150, 153]]);

        //Logo
        $this->pdf->image(storage_path('app/' . $this->disciplina->instituicao->sigla . '.png'), 10, 10, 90, 16.87);
    }

    public function adiciona(Disciplina $disciplina)
    {
        $this->paginasDisciplina = 2;

        $this->linha1 = ['width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
        $this->disciplina = $disciplina;

        //Adiciona a primeira página
        $this->novaPagina('ANÁLISE GRÁFICA');

        //Perguntas - 1ª Página
        $q = 0;
        foreach (Pergunta::orderBy('numero')->get() as $pergunta) {
            if ($pergunta->tipo < 3) {
                $this->graficoPergunta(
                    $pergunta,
                    10 + ($q % 3) * 65,
                    84 + (intdiv($q, 3) * 65)
                );
                $q++;
            }
        }

        //Legenda
        $this->pdf->SetLineStyle($this->linha1);
        $this->pdf->SetFillColor(230);
        $this->pdf->RoundedRect(140, 250, 60, 22, 2, '1111', 'DF');

        $this->pdf->SetFillColor(220, 133, 61);
        $this->pdf->Rect(150, 258, 10, 4, 'DF');

        $this->pdf->SetFillColor(65, 66, 146);
        $this->pdf->Rect(150, 265, 10, 4, 'DF');

        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetXY(141, 251);
        $this->pdf->Cell(0, 0, 'Legenda');

        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->SetXY(162, 257.5);
        $this->pdf->Cell(0, 0, 'Alunos');
        $this->pdf->SetXY(162, 264.5);
        $this->pdf->Cell(0, 0, 'Professores');

        //Adiciona a segunda página
        $this->novaPagina('PLANILHA DE RESULTADOS');

        //Desenha a base da tabela de perguntas
        $total = [
            'A' => [],
            'P' => []
        ];

        //Quantidade de respostas válidas para calcular a média
        $qM = [
            'A' => 0,
            'P' => 0,
        ];

        $q = 0;
        $perguntas = Pergunta::orderBy('numero')->get();
        foreach ($perguntas as $pergunta) {
            if ($pergunta->tipo < 3) {
                $this->pdf->Rect(10, 100 + 7 * $q, 190, 7, 'D', ['all' => $this->linha1]);

                $this->pdf->setFont('helvetica', 'B', 23);
                $this->pdf->SetTextColor(188, 189, 191);
                $this->pdf->SetXY(10, 98.2 + 7 * $q);
                $this->pdf->Cell(10, 0, str_pad($pergunta->numero, 2, '0', STR_PAD_LEFT));

                $this->pdf->setFont('helvetica', '', 7);
                $this->pdf->SetTextColor(0);
                $this->pdf->SetXY(20, 100 + 7 * $q);
                $this->pdf->MultiCell(98, 7, $pergunta->texto, 0, 'L');

                //Realiza os cálculos das respostas
                $total['A'][$pergunta->numero] = $pergunta->tipo == 1 ? [1=>0, 2=>'-', 3=>0, 4=>0] : [1=>0, 2=>0, 3=>0, 4=>0];
                $total['P'][$pergunta->numero] = $pergunta->tipo == 1 ? [1=>0, 2=>'-', 3=>0, 4=>0] : [1=>0, 2=>0, 3=>0, 4=>0];
                $media['A'][$pergunta->numero] = 0;
                $media['P'][$pergunta->numero] = 0;

                $quant = ['A'=>0, 'P'=>0];
                $quantG = ['A'=>0, 'P'=>0];

                $respostas = $this->disciplina->respostas()->where('pergunta_id', $pergunta->id)->get();

                foreach ($respostas as $resposta) {
                    $total[$resposta->tipo][$pergunta->numero][$resposta->resposta]++;
                    if ($pergunta->tipo == 1) {
                        $nota = [10, 0, 0, 0];
                    } else {
                        $nota = [10, 5, 0, 0];
                    }
                    $media[$resposta->tipo][$pergunta->numero] += $nota[$resposta->resposta - 1];
                    if ($resposta->resposta != 4) {
                        $quant[$resposta->tipo]++;
                    }
                    $quantG[$resposta->tipo]++;
                }

                if ($quant['A']) {
                    $media['A'][$pergunta->numero] = round($media['A'][$pergunta->numero], 2) / $quant['A'];
                }
                if ($quant['P']) {
                    $media['P'][$pergunta->numero] = round($media['P'][$pergunta->numero], 2) / $quant['P'];
                }

                if ($media['A'][$pergunta->numero]) {
                    $r = hexdec($this->cores[intval($media['A'][$pergunta->numero])][0]);
                    $g = hexdec($this->cores[intval($media['A'][$pergunta->numero])][1]);
                    $b = hexdec($this->cores[intval($media['A'][$pergunta->numero])][2]);
                } else {
                    $r = $g = $b = 220;
                }
                $this->pdf->Rect(120, 100 + 7 * $q, 40, 7, 'DF', ['all'=>$this->linha1], [$r, $g, $b]);

                if ($media['P'][$pergunta->numero]) {
                    $r = hexdec($this->cores[intval($media['P'][$pergunta->numero])][0]);
                    $g = hexdec($this->cores[intval($media['P'][$pergunta->numero])][1]);
                    $b = hexdec($this->cores[intval($media['P'][$pergunta->numero])][2]);
                } else {
                    $r = $g = $b = 220;
                }
                $this->pdf->Rect(160, 100 + 7 * $q, 40, 7, 'DF', ['all'=>$this->linha1], [$r, $g, $b]);

                $this->pdf->setFont('helvetica', '', 8);
                $this->pdf->SetTextColor(0);
                $this->pdf->SetXY(120, 102 + 7 * $q);
                $this->pdf->MultiCell(20, 7, implode(' / ', $total['A'][$pergunta->numero]));

                $this->pdf->SetXY(160, 102 + 7 * $q);
                $this->pdf->MultiCell(20, 7, implode(' / ', $total['P'][$pergunta->numero]));

                $this->pdf->setFont('helvetica', '', 14);
                $this->pdf->SetXY(140, 100.5 + 7 * $q);
                $this->pdf->MultiCell(20, 7, $media['A'][$pergunta->numero] ? number_format(round($media['A'][$pergunta->numero], 2), 2) : '-', 0, 'C');

                $this->pdf->SetXY(180, 100.5 + 7 * $q);
                $this->pdf->MultiCell(20, 7, $media['P'][$pergunta->numero] ? number_format(round($media['P'][$pergunta->numero], 2), 2) : '-', 0, 'C');

                if ($media['A'][$pergunta->numero]) {
                    $qM['A']++;
                }
                if ($media['P'][$pergunta->numero]) {
                    $qM['P']++;
                }

                $q++;
            }
        }

        $m = array_sum($media['A']) ? number_format(array_sum($media['A']) / $qM['A'] /*count($media['A'])*/, 2) : '-';
        $r = $m != '-' ? hexdec($this->cores[intval($m)][0]) : 220;
        $g = $m != '-' ? hexdec($this->cores[intval($m)][1]) : 220;
        $b = $m != '-' ? hexdec($this->cores[intval($m)][2]) : 220;
        $this->pdf->Rect(120, 100 + 7 * $q, 40, 7, 'DF', ['all'=>$this->linha1], [$r, $g, $b]);

        $this->pdf->setFont('helvetica', 'B', 18);
        $this->pdf->SetXY(120, 99.5 + 7 * $q);
        $this->pdf->MultiCell(40, 7, $m, 0, 'C');

        $m = array_sum($media['P']) ? number_format(array_sum($media['P']) / $qM['P'] /*count($media['P'])*/, 2) : '-';
        $r = $m != '-' ? hexdec($this->cores[intval($m)][0]) : 220;
        $g = $m != '-' ? hexdec($this->cores[intval($m)][1]) : 220;
        $b = $m != '-' ? hexdec($this->cores[intval($m)][2]) : 220;
        $this->pdf->Rect(160, 100 + 7 * $q, 40, 7, 'DF', ['all'=>$this->linha1], [$r, $g, $b]);

        $this->pdf->setFont('helvetica', 'B', 18);
        $this->pdf->SetXY(160, 99.5 + 7 * $q);
        $this->pdf->MultiCell(40, 7, $m, 0, 'C');

        $this->pdf->Line(120, 86, 200, 86);
        $this->pdf->Line(120, 93, 200, 93);
        $this->pdf->Rect(10, 100 + 7 * (sizeof($perguntas) - 1), 190, 7);
        $this->pdf->Line(120, 86, 120, 100 + 7 * sizeof($perguntas));
        $this->pdf->Line(140, 93, 140, 100 + 7 * (sizeof($perguntas) - 1));
        $this->pdf->Line(160, 86, 160, 100 + 7 * sizeof($perguntas));
        $this->pdf->Line(180, 93, 180, 100 + 7 * (sizeof($perguntas) - 1));
        $this->pdf->Line(200, 86, 200, 100 + 7 * sizeof($perguntas));

        $this->pdf->setFont('helvetica', 'B', 10);
        $this->pdf->SetXY(10, 101 + 7 * $q);
        $this->pdf->MultiCell(98, 7, 'Média', 0, 'L');

        $this->pdf->setFont('helvetica', 'B', 10);
        $this->pdf->SetXY(120, 87.5);
        $this->pdf->MultiCell(40, 7, 'Alunos: ' . $quantG['A'], 0, 'C');

        $this->pdf->SetXY(160, 87.5);
        $this->pdf->MultiCell(40, 7, 'Professores: ' . $quantG['P'], 0, 'C');

        $this->pdf->SetXY(120, 94.5);
        $this->pdf->MultiCell(20, 7, 'Respostas', 0, 'C');

        $this->pdf->SetXY(140, 94.5);
        $this->pdf->MultiCell(20, 7, 'Nota', 0, 'C');

        $this->pdf->SetXY(160, 94.5);
        $this->pdf->MultiCell(20, 7, 'Respostas', 0, 'C');

        $this->pdf->SetXY(180, 94.5);
        $this->pdf->MultiCell(20, 7, 'Nota', 0, 'C');

        //Questões discursivas

        //Primeiro título
        $this->pdf->SetXY(9.5, 170);
        $this->pdf->SetTextColor(149, 150, 153);
        $this->pdf->SetFont('helvetica', '', 18);
        $this->pdf->Cell(70, 0, 'QUESTÕES DISCURSIVAS');
        $this->pdf->Line(10, 178, 200, 178, ['width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [149, 150, 153]]);

        $y = 180;
        foreach (Pergunta::orderBy('numero')->get() as $pergunta) {
            if ($pergunta->tipo == 4) {
                $y = $this->perguntaDiscursiva(
                    $pergunta,
                    $y
                );
                $q++;
            }
        }

        //Ajusta o número de páginas para sempre par
        if ($this->paginasDisciplina % 2) {
            $this->pdf->AddPage();
            $this->pagina++;
        }
    }

    public function perguntaDiscursiva($pergunta, $y)
    {
        //Escreve o enunciado
        $this->pdf->Rect(10, $y + 0.8, 190, 7, 'D', ['all' => $this->linha1]);

        $this->pdf->setFont('helvetica', 'B', 23);
        $this->pdf->SetTextColor(188, 189, 191);
        $this->pdf->SetXY(10, $y - 0.8);
        $this->pdf->Cell(10, 0, str_pad($pergunta->numero, 2, '0', STR_PAD_LEFT));

        $this->pdf->setFont('helvetica', '', 7);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetXY(20, $y + 0.8);
        $this->pdf->MultiCell(190, 7, $pergunta->texto, 0, 'L');

        //Lista as respostas
        $respostas = $this->disciplina->respostas()->where('pergunta_id', $pergunta->id)->get();

        $y += 10;

        foreach ($respostas as $resposta) {
            $texto = $resposta->resposta;//rtrim(rtrim($resposta->resposta));

            $this->pdf->setFont('helvetica', '', 10);

            //@doing

            if ($y + $this->alturaTexto($texto) > 280) {
                $this->novaPagina('QUESTÕES DISCURSIVAS');
                $this->paginasDisciplina++;
                $y = 82;
            }

            $this->pdf->setFont('helvetica', '', 10);
            $this->pdf->SetTextColor(0);
            $this->pdf->SetXY(10, $y);
            $this->pdf->MultiCell(190, 0, $texto, 0, 'L');

            $y += $this->alturaTexto($texto) + $this->alturaTexto('W') / 2;
            $this->pdf->Line(10, $y, 200, $y, ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [149, 150, 153]]);
            $y += $this->alturaTexto('W') / 2;
        }
    }

    public function graficoPergunta(Pergunta $pergunta, $x, $y)
    {
        //"Borda" da questão
        // $this->pdf->SetLineStyle($this->linha1);
        // $this->pdf->Rect($x, $y, 60, 60, 'D');

        //Número da questão
        $this->pdf->setFont('times', 'B', 30);
        $this->pdf->SetTextColor(188, 189, 191);

        $this->pdf->SetXY($x - 2, $y + 10);
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90);
        $this->pdf->Cell(10, 0, str_pad($pergunta->numero, 2, '0', STR_PAD_LEFT));
        $this->pdf->StopTransform();

        //Texto da questão
        $this->pdf->setFont('helvetica', '', 9);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetXY($x + 8, $y - 1);
        $this->pdf->MultiCell(47, 20, $pergunta->texto, 0, 'L');

        //Estrutura do gráfico
        $this->pdf->setFont('helvetica', '', 9);
        $this->pdf->SetTextColor(0);

        //Eixo Y
        for ($i = 0; $i <= 100; $i += 25) {
            $this->pdf->SetXY($x, $y + 53 - $i / 3);
            $this->pdf->Cell(7, 0, $i, 0, 0, 'R');
            $this->pdf->SetLineStyle(['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => $i ? '1,1' : 0, 'color' => [$i ? 128 : 64]]);
            $this->pdf->line($x + 7, $y + 55 - $i / 3, $x + 55, $y + 55 - $i / 3);
        }

        //Eixo X
        $eixoX = $pergunta->tipo == 1 ? [1=>'S', 3=>'N', 4=>'NR'] : [1=>'S', 2=>'AV', 3=>'N', 4=>'NR'];
        $w = 0;
        foreach ($eixoX as $n => $i) {
            $this->pdf->SetXY($x + 7 + (48 / sizeof($eixoX)) * $w, $y + 55);
            $this->pdf->Cell((48 / sizeof($eixoX)), 0, $i, 0, 0, 'C');

            $this->pdf->Line(
                $x + 7 + (48 / sizeof($eixoX)) * $w,
                $y + 54,
                $x + 7 + (48 / sizeof($eixoX)) * $w,
                $y + 56,
                $this->linha1
            );
            $w++;
        }

        //Busca as respostas
        $total = [
            'A' => [],
            'P' => []
        ];

        foreach ($eixoX as $n => $s) {
            $total['A'][$n] = 0;
            $total['P'][$n] = 0;
        }

        $respostas = $this->disciplina->respostas()->where('pergunta_id', $pergunta->id)->get();
        foreach ($respostas as $resposta) {
            $total[$resposta->tipo][$resposta->resposta]++;
        }

        $w = 0;
        foreach ($eixoX as $n => $i) {
            //Série "Aluno"
            if (array_sum($total['A'])) {
                $perc = $total['A'][$n] / array_sum($total['A']);
                $this->pdf->SetFillColor(220, 133, 61);
                $this->pdf->Rect(
                    $x + 7 + ($w + 0.1) * 48 / sizeof($eixoX),
                    $y + 55 - 33.3333 * $perc,
                    48 / sizeof($eixoX) * 0.4,
                    $perc * 33.3333,
                    'F'
                );
            }

            //Série "Professor"
            if (array_sum($total['P'])) {
                $perc = $total['P'][$n] / array_sum($total['P']);
                $this->pdf->SetFillColor(65, 66, 146);
                $this->pdf->Rect(
                    $x + 7 + ($w + 0.1) * 48 / sizeof($eixoX) + 48 / sizeof($eixoX) * 0.4,
                    $y + 55 - 33.3333 * $perc,
                    48 / sizeof($eixoX) * 0.4,
                    $perc * 33.3333,
                    'F'
                );
            }
            $w++;
        }
    }

    private function alturaTexto($txt, $w = 190, $border = 0)
    {
        return $this->pdf->getStringHeight($w, $txt);
        // store current object
        $this->pdf->startTransaction();
        // store starting values
        $start_y = $this->pdf->GetY();
        $start_page = $this->pdf->getPage();
        // call your printing functions with your parameters
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $this->pdf->MultiCell($w = 0, $h = 0, $txt, $border = 1, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0);
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // get the new Y
        $end_y = $this->pdf->GetY();
        $end_page = $this->pdf->getPage();
        // calculate height
        $height = 0;
        if ($end_page == $start_page) {
            $height = $end_y - $start_y;
        } else {
            for ($page = $start_page; $page <= $end_page; ++$page) {
                $this->pdf->setPage($page);
                if ($page == $start_page) {
                    // first page
                    $height = $this->h - $start_y - $this->bMargin;
                } elseif ($page == $end_page) {
                    // last page
                    $height = $end_y - $this->tMargin;
                } else {
                    $height = $this->h - $this->tMargin - $this->bMargin;
                }
            }
        }
        // restore previous object
        $this->pdf = $this->pdf->rollbackTransaction();

        return $height;
    }

    public function salva()
    {
        echo(storage_path('app/temp.pdf'));
        $this->pdf->Output(storage_path('app/temp.pdf'), 'F');
    }
}
