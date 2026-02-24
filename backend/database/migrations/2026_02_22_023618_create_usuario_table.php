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
        Schema::create('Usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre', 25);
            $table->string('ap_paterno', 25);
            $table->string('ap_materno', 25);
            $table->date('fecha_nacimiento');
            $table->string('sexo', 9);
            $table->string('telefono', 10)->unique();
            $table->string('correo', 64);
            $table->string('rfc', 13)->unique();
            $table->string('curp', 18)->unique();
            $table->float('salario');
            $table->string('status', 9);
            $table->timestamp('ultimo_login');
            $table->integer('intentos_fallidos');
            $table->timestamp('fecha_entrada');
            $table->string('contrasena', 60); // Nota: bcrypt suele requerir 60 chars
            $table->integer('num_ext');
            $table->integer('num_int')->nullable();
            $table->string('calle', 30);
            $table->string('colonia', 30);
            $table->string('municipio', 30);
            $table->string('estado', 30);
            
            $table->unsignedInteger('id_rol');
            $table->foreign('id_rol')->references('id_rol')->on('Rol');
            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Usuario');
    }
};
