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
        Schema::create('historial_asignaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asignado_id');
            $table->unsignedBigInteger('funcionario_id');
            $table->unsignedBigInteger('peticion_id');
            $table->timestamp('fecha_asignacion')->useCurrent();

            $table->foreign('asignado_id')->references('id')->on('asignacions')->onDelete('cascade');
            $table->foreign('funcionario_id')->references('id')->on('funcionarios')->onDelete('cascade');
            $table->foreign('peticion_id')->references('id')->on('peticiones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_asignaciones');
    }
};
