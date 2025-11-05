<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id('id_contrato');
            $table->foreignId('id_inmueble')
                  ->constrained('inmuebles', 'id_inmueble')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('id_arrendador')
                  ->constrained('usuarios', 'id_usuario')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('id_arrendatario')
                  ->constrained('usuarios', 'id_usuario')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('valor_mensual', 12, 2);
            $table->decimal('deposito_garantia', 12, 2)->default(0.00);
            $table->text('clausulas')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado_contrato', ['Activo', 'Finalizado', 'Cancelado'])->default('Activo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contratos');
    }
};