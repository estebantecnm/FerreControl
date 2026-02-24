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
        Schema::create('Proveedor', function (Blueprint $table) {
            $table->increments('id_proveedor');
            $table->string('nombre_contacto', 30);
            $table->string('telefono_contacto', 10)->unique();
            $table->string('correo_contacto', 64);
            $table->string('nombre', 50);
            $table->string('telefono', 10)->unique();
            $table->string('tiempo_entrega', 20);
            $table->string('correo', 64);
            $table->string('tipo', 20);
            $table->string('rfc', 13)->unique();
            $table->integer('num_int')->nullable();
            $table->integer('num_ext');
            $table->string('pais', 30);
            $table->string('estado', 30);
            $table->string('municipio', 30);
            $table->string('ciudad', 30);
            $table->string('colonia', 30); 
            $table->string('calle', 30);
            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Proveedor');
    }
};
