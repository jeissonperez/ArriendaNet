<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('solicitudes_mantenimiento', function (Blueprint $table) {
            $table->id('id_solicitud');
            $table->foreignId('id_inmueble')
                  ->constrained('inmuebles', 'id_inmueble')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('id_usuario')
                  ->constrained('usuarios', 'id_usuario')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->dateTime('fecha_solicitud')->useCurrent();
            $table->enum('tipo_mantenimiento', ['Eléctrico', 'Plomería', 'Estructural', 'Electrodomésticos', 'Pintura', 'Otro']);
            $table->text('descripcion');
            $table->enum('prioridad', ['Baja', 'Media', 'Alta', 'Urgente'])->default('Media');
            $table->enum('estado_solicitud', ['Pendiente', 'En proceso', 'Finalizado', 'Rechazado'])->default('Pendiente');
            $table->dateTime('fecha_atencion')->nullable();
            $table->text('observaciones_arrendador')->nullable();
            $table->string('evidencia_foto', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitudes_mantenimiento');
    }
};