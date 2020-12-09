<?php

namespace App\Http\Controllers;

use App\Pergunta;
use App\Disciplina;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisciplinaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('disciplinas');
    }

    public function show(Disciplina $disciplina, User $professor)
    {
        //Verifica se já foi respondida
        $user = Auth::user();
        // dump($user);
        // dump($disciplina->id);
        // dump($professor->id);
        // dump($user->disciplinas()->where('id', $disciplina->id)->wherePivot('professor_id', $professor->id)->first());
        if (
            !$user->disciplinas()->where('id', $disciplina->id)->wherePivot('professor_id', $professor->id)->first() ||
            $user->disciplinas()->where('id', $disciplina->id)->wherePivot('professor_id', $professor->id)->wherePivot('respondido', true)->first()
        ) {
            // if ($user->disciplinas->find($disciplina->id)->pivot->respondido) {
            return redirect('/disciplinas');
        }
        // $professor = User::find($user->disciplinas->find($disciplina->id)->pivot->professor_id);

        $perguntas = Pergunta::orderBy('numero')->get();

        return view('disciplina', compact('disciplina', 'perguntas', 'professor'));
    }

    public function store(Disciplina $disciplina, User $professor, Request $request)
    {
        if (!$disciplina) {
            return response('', 401);
        }

        return response('', $disciplina->grava($professor, $request));
    }
}
