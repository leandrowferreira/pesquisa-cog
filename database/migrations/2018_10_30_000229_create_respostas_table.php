<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRespostasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respostas', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('disciplina_id');
            $table->unsignedInteger('pergunta_id');
            $table->unsignedInteger('professor_id');
            $table->string('turma', 20)->nullable();

            $table->enum('tipo', ['A', 'P'])->comment('A: Aluno; P: Professor');
            $table->text('resposta')->nullable();

            $table->boolean('feedback')->default(false);
            $table->string('nome')->nullable();
            $table->string('email')->nullable();

            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->foreign('pergunta_id')->references('id')->on('perguntas')->onDelete('cascade');
            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('respostas');
    }
}
