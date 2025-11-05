<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('doc_usuario', 45)->unique();
            $table->enum('tip_documento', ['CC', 'CE', 'TI', 'PA', 'DNI', 'PEP', 'PPT']);
            $table->string('pri_nombre', 45);
            $table->string('seg_nombre', 45)->nullable();
            $table->string('pri_apellido', 45);
            $table->string('seg_apellido', 45);
            $table->date('fec_nacimiento');
            $table->enum('sex_usuario', ['Masculino', 'Femenino']);
            $table->string('cel_usuario', 45)->unique();
            $table->string('correo_usuario', 45)->unique();
            $table->string('contraseña', 100);
            $table->enum('rol_usuario', ['Arrendador', 'Arrendatario'])->default('Arrendatario');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};