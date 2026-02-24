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
        Schema::create('Detalle_pedido', function (Blueprint $table) {
            $table->increments('id_detalle'); // SERIAL PK
            $table->integer('cantidad');
            $table->float('precio_unitario');
            
            // Llaves Foráneas
            $table->unsignedInteger('id_pedido_cliente');
            $table->unsignedInteger('id_producto');
            
            // Restricciones según tu DDL
            $table->foreign('id_pedido_cliente')->references('id_pedido_cliente')->on('Pedido_cliente');
            $table->foreign('id_producto')->references('id_producto')->on('Producto');
            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Detalle_pedido');
    }
};
