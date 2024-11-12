<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cedula')->unique();
            $table->string('nombre');
            $table->string('email');
            $table->unsignedBigInteger('asignado_id');
            $table->foreign('asignado_id')->references('id')->on('asignacions');
            $table->string('area')->require();
            $table->string('lider_area')->default(false);
            $table->string('director')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
