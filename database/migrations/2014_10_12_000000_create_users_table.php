<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('instituicao_id')->nullable();

            $table->string('nome');
            $table->string('matricula')->unique()->nullable();
            $table->string('turma', 20)->nullable();
            $table->string('grupo', 20)->nullable();
            $table->boolean('aviso_privacidade')->default(true);
            $table->string('tipo', 1);
            $table->string('remember_token')->nullable();
            $table->string('md5')->nullable();

            $table->foreign('instituicao_id')->references('id')->on('instituicoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
