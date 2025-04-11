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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name');
            $table->string('name_ar');
            $table->string('first_name_ar');
            $table->string('sum_number')->unique();
            $table->string('email')->unique();
            $table->string('city'); // Ajout de la ville
            $table->boolean('status')->default(1); // Ajout du statut (0 ou 1) avec une valeur par défaut
            $table->integer('limit'); // Ajout de la limite (numérique)
            $table->string('grad');
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
        Schema::dropIfExists('teachers');
    }
};
