<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {
        Schema::create('Movimiento_stock', function (Blueprint $table) {
            $table->increments('id_movimiento');
            $table->string('tipo_movimiento', 30); // Entrada/Salida
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            
            $table->unsignedInteger('id_producto');
            $table->foreign('id_producto')->references('id_producto')->on('Producto');

            $table->unsignedInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('Usuario');
            
            $table->timestamps(); // create_at servirá como fecha_movimiento
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Movimiento_stock');
    }
};