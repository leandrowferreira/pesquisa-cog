<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplinaUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disciplina_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('disciplina_id');
            $table->unsignedInteger('professor_id');
            $table->boolean('respondido')->default(false);
            $table->ipAddress('ip')->nullable()->default(null);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disciplina_user');
    }
}
