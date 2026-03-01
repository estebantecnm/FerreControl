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
        Schema::create('Detalle_venta', function (Blueprint $table) {
            $table->increments('id_detalle_venta');
            $table->integer('cantidad');
            $table->float('precio_unitario');
            
            $table->unsignedInteger('id_venta');
            $table->unsignedInteger('id_producto');
            
            $table->foreign('id_venta')->references('id_venta')->on('Venta');
            $table->foreign('id_producto')->references('id_producto')->on('Producto');
            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Detalle_venta');
    }
};
