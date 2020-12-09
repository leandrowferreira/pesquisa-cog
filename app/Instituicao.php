<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instituicao extends Model
{
    protected $table = 'instituicoes';

    public function disciplinas()
    {
        return $this->hasMany(Disciplina::class);
    }
}
