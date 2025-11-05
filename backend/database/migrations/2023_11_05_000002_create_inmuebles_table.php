<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inmuebles', function (Blueprint $table) {
            $table->id('id_inmueble');
            $table->foreignId('id_usuario')->nullable()
                  ->constrained('usuarios', 'id_usuario')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            $table->string('direccion', 100);
            $table->enum('tipo_inmueble', ['Terreno', 'Casa', 'Apartamento', 'Local', 'Lote', 'Otro']);
            $table->decimal('valor_arriendo', 12, 2);
            $table->enum('estado', ['Disponible', 'Arrendado', 'Mantenimiento'])->default('Disponible');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inmuebles');
    }
};