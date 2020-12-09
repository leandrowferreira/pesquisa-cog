@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="m-0">Suas disciplinas</h4>
        </div>
        <div class="card-body">

            <ul class="list-group">
                @foreach(Auth::user()->disciplinas as $disciplina)
                    <li class="list-group-item {{$disciplina->pivot->respondido ? 'list-group-item-success' : 'list-group-item-info'}}">

                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">
                            <span class="small">{{$disciplina->codigo}}</span><br />
                            <strong>{{App\User::find($disciplina->pivot->professor_id)->nome}}</strong><br>
                            {{$disciplina->nome}}
                            </h5>
                            <span class="d-none d-sm-block">
                                @if (!$disciplina->pivot->respondido)
                                <a class="btn btn-sm btn-primary" href="/disciplinas/{{$disciplina->id}}/{{$disciplina->pivot->professor_id}}">Responder <font-awesome-icon class="ml-1" icon="arrow-circle-right"></font-awesome-icon></a>
                                @else
                                <!-- <span class="badge p-2 badge-success"><font-awesome-icon icon="check-circle"></font-awesome-icon> Respondido</span> -->
                                @endif
                            </span>
                        </div>
                        @foreach($disciplina->professores as $professor)
                        <span class="text-muted small">{{$professor->nome}}</span><br>
                        @endforeach
                        @if (!$disciplina->pivot->respondido)
                        <a class="d-inline d-sm-none btn btn-sm btn-primary my-2" href="/disciplinas/{{$disciplina->id}}">Responder <font-awesome-icon class="ml-1" icon="arrow-circle-right"></font-awesome-icon></a>
                        @else
                        <p class="small mb-0 pt-1"><strong>Já respondido</strong></p>
                        <!-- <span class="d-inline d-sm-none badge p-2 badge-success"><font-awesome-icon icon="check-circle" /></font-awesome-icon> Respondido</span> -->
                        @endif

                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @auth
        <a href="/logout" class="btn btn-link mt-2">Sair da pesquisa</a>
    @endauth

</div>
@endsection