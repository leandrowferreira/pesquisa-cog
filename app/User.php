<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['aviso_privacidade'];
    public $timestamps  = false;

    public function disciplinas()
    {
        return $this->belongsToMany(Disciplina::class)->withPivot(['respondido', 'ip', 'professor_id'])->orderBy('nome');
    }
}
