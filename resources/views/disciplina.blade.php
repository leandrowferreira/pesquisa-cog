@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-primary" role="alert">
        <h4 class="mb-0 alert-heading">{{$professor->nome}}</h4>
        <span class="text-muted">{{$disciplina->nome}}</span><br>
    </div>

    <pesq-perguntas disc-id="{{$disciplina->id}}" prof-id="{{$professor->id}}"></pesq-perguntas>

    <div class="row">
        <div class="col-12 text-center">
            <a class="btn btn-link mt-2" href="/disciplinas">Sair sem salvar</a>
        </div>
    </div>

</div>
@endsection