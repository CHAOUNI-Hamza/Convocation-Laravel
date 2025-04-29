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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('apogee')->unique();
            $table->string('cne');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('last_name_ar');
            $table->string('first_name_ar');
            $table->string('cnie');
            $table->date('birth_date');
            $table->string('lab');
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
        Schema::dropIfExists('students');
    }
};
