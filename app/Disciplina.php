<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    protected $table = 'disciplinas';

    protected function pretty($n)
    {
        $n = Str::ascii($n);
        $n = explode(' ', mb_convert_case($n, MB_CASE_TITLE, 'UTF-8'));
        foreach ($n as $i => $p) {
            if (strlen($p) <= 2) {
                $n[$i] = mb_convert_case($p, MB_CASE_LOWER, 'UTF-8');
            }

            if (strpos('i ii Iii iv v vi Vii Viii ix x', $p) !== false) {
                $n[$i] = mb_convert_case($p, MB_CASE_UPPER, 'UTF-8');
            }
        }

        return implode(' ', $n);
    }

    public function getNomeAttribute($n)
    {
        return $this->pretty($n);
    }

    public function getProfessorAttribute($n)
    {
        return $this->pretty($n);
    }

    //Grava as respostas e retorna um código
    public function grava($professor, $request)
    {
        //Grava as respostas
        foreach ($request->all() as $num => $resposta) {
            $pergunta = Pergunta::where('numero', $num)->first();
            $resposta = Resposta::novo($this, $professor, $pergunta, $resposta);

            if (!$pergunta || !$resposta) {
                return 402;
            }
        }

        //Marca a disciplina como respondida
        Auth::user()
            ->disciplinas()
            ->wherePivot('professor_id', $professor->id)
            ->updateExistingPivot($this->id, ['respondido' => true, 'ip' => $request->ip()]);

        return 200;
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['respondido', 'ip']);
    }

    public function professores()
    {
        return $this->belongsToMany(User::class, 'disciplina_professor', 'disciplina_id');
    }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }
}
