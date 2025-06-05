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
            $table->string('name_ar')->nullable();
            $table->string('first_name_ar')->nullable();
            $table->string('sum_number');
            $table->string('email')->unique();
            $table->string('city')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('limit')->nullable();
            $table->string('grad')->nullable();
            $table->string('day_time')->nullable();
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
