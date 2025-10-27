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
        Schema::create('cambio_producto', function (Blueprint $table) {
            $table->id('id_cambio');
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_cambio')->nullable();
            $table->string('motivo', 255);
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'completado']);
            $table->unsignedBigInteger('id_producto');
            $table->integer('cantidad');
            $table->unsignedBigInteger('id_proveedor');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamps();

            $table->foreign('id_producto')->references('id_producto')->on('producto');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedor');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cambio_producto');
    }
};