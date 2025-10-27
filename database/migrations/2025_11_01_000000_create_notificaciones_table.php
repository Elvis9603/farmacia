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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('id_notificacion');
            $table->string('titulo', 100);
            $table->text('mensaje');
            $table->string('tipo', 50);
            $table->dateTime('fecha_creacion');
            $table->dateTime('fecha_limite')->nullable();
            $table->boolean('leida')->default(false);
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('enlace', 100)->nullable();
            $table->boolean('enviada_email')->default(false);
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};