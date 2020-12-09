<?php

namespace App\Http\Controllers;

use App\Pergunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerguntaController extends Controller
{
    public function index()
    {
        return Pergunta::where('grupo', Auth::user()->grupo)->orderBy('numero')->get();
    }
}
