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
        Schema::create('Especificacion', function (Blueprint $table) {
            $table->increments('id_especificacion');
            $table->string('nombre_atributo', 30);
            $table->string('valor', 20);
            
            $table->unsignedInteger('id_producto');
            $table->foreign('id_producto')->references('id_producto')->on('Producto');
            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Especificacion');
    }
};
