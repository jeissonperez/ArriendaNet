<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cuentas_cobro', function (Blueprint $table) {
            $table->id('id_cuenta');
            $table->foreignId('id_contrato')
                  ->constrained('contratos', 'id_contrato')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->date('fecha_emision');
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->decimal('valor_arriendo', 12, 2);
            $table->decimal('otros_cargos', 12, 2)->default(0.00);
            $table->decimal('total_pagar', 12, 2);
            $table->enum('estado_pago', ['Pendiente', 'Pagado', 'Vencido'])->default('Pendiente');
            $table->enum('metodo_pago', ['Efectivo', 'Transferencia', 'Consignacion', 'Otro'])->default('Transferencia');
            $table->date('fecha_pago')->nullable();
            $table->string('numero_factura', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('documento_pdf', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuentas_cobro');
    }
};