<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerguntasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perguntas', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedTinyInteger('numero');
            $table->unsignedTinyInteger('tipo')->comment('1: S/N; 2: S/AV/N/NS; 3: 1-3; 4: texto');
            $table->string('grupo', 20)->nullable();
            $table->boolean('feedback')->default(false);
            $table->text('texto');
            $table->string('resposta1')->nullable();
            $table->string('resposta2')->nullable();
            $table->string('resposta3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perguntas');
    }
}
