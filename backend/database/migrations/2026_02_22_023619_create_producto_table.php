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
        Schema::create('Producto', function (Blueprint $table) {
            $table->increments('id_producto');
            $table->string('nombre', 50);
            $table->string('marca', 25);
            $table->float('precio_venta');
            $table->float('precio_compra');
            $table->float('utilidad');
            $table->bigInteger('codigo_barras')->unique();
            $table->string('status', 20);
            $table->string('unidad_medida', 25);
            $table->integer('cantidad_presentacion');
            $table->string('color', 20);
            $table->integer('cantidad_inicial')->nullable();
            
            $table->unsignedInteger('id_categoria');
            $table->foreign('id_categoria')->references('id_categoria')->on('Categoria');

            //relacion con usuario
            $table->unsignedInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('Usuario');

            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Producto');
    }
};
