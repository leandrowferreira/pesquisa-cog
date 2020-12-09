@extends('layouts.app')

@section('content')
<div class="container">
    <div class="jumbotron pt-0">
        <div class="row">
            <div class="col-md-8">
                <h1 class="display-5">Avaliação dos<br>Professores</h1>
            </div>
            <div class="col-md-4 col-sm-6 col-6">
                <img class="img-fluid" src="img/cog.png">
            </div>
        </div>

        <hr class="my-4">

        <p>
           Este questionário tem como objetivo medir suas impressões sobre o corpo
           docente no decorrer do ano letivo de 2020.
        </p>
        <p>
            A pesquisa é totalmente sigilosa. Suas respostas não serão associadas
            a você. A única informação que guardamos a seu respeito é se você
            respondeu ou não a pesquisa.
        </p>


        @auth
        <a href="/disciplinas" class="btn btn-success btn-lg">Responder à pesquisa</a>
        @endauth
        <hr class="my-4">

        @auth
        <a href="/logout" class="btn btn-link mt-2">Sair da pesquisa</a>
        @endauth
        @guest
        <a href="/login" class="btn btn-primary md-2">Acessar</a>
        @endguest


    </div>

</div>
@endsection
