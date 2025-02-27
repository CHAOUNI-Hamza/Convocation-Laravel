<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('creneau_horaire'); // Format 00:00
            $table->string('module');
            $table->string('salle');
            $table->string('filiere');
            $table->string('semestre');
            $table->string('groupe');
            $table->string('lib_mod');
            $table->json('teacher_ids')->nullable(); // Stockera un tableau d'IDs d'enseignants
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
};
