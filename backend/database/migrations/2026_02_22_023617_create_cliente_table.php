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
        Schema::create('Cliente', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->string('nombre', 25);
            $table->string('ap_paterno', 25);
            $table->string('ap_materno', 25);
            $table->date('fecha_nacimiento');
            $table->string('sexo', 9);
            $table->string('correo', 64);
            $table->string('rfc', 13)->unique();
            $table->string('telefono', 10)->unique();
            $table->integer('num_ext');
            $table->integer('num_int')->nullable();
            $table->string('calle', 30);
            $table->string('colonia', 30);
            $table->string('municipio', 30);
            $table->string('estado', 30);
            $table->string('status', 30)->default('activo');
            $table->float('limite_credito', 10, 2)->nullable();
            $table->float('saldo_pendiente', 10, 2)->default(0);
            $table->integer('dias_credito')->nullable();
            $table->string('tipo_cliente', 30);


            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Cliente');
    }
};
