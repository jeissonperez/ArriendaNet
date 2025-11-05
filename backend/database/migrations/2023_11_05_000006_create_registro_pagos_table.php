<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registro_pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->foreignId('id_cuenta')
                  ->constrained('cuentas_cobro', 'id_cuenta')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('id_arrendatario')
                  ->constrained('usuarios', 'id_usuario')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->decimal('monto_pagado', 12, 2);
            $table->date('fecha_pago');
            $table->enum('metodo_pago', ['Efectivo', 'Transferencia', 'Consignación', 'Otro'])->default('Transferencia');
            $table->string('referencia_pago', 100)->nullable();
            $table->enum('estado_verificacion', ['Pendiente', 'Verificado', 'Rechazado'])->default('Pendiente');
            $table->text('descripcion_soporte')->nullable();
            $table->string('archivo_soporte', 255)->nullable();
            $table->dateTime('fecha_subida_soporte')->useCurrent();
            $table->foreignId('verificado_por')
                  ->nullable()
                  ->constrained('usuarios', 'id_usuario')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registro_pagos');
    }
};