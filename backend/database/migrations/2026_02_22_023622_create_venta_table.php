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
        Schema::create('Venta', function (Blueprint $table) {
            $table->increments('id_venta');
            $table->date('fecha_venta');
            $table->float('total_venta');
            $table->string('metodo_pago', 20);
            $table->integer('pago_cliente');
            $table->float('cambio');
            
            $table->unsignedInteger('id_cliente');
            $table->foreign('id_cliente')->references('id_cliente')->on('Cliente');

            //de qué pedido nació cada venta para tener trazabilidad
            $table->unsignedInteger('id_pedido_cliente');
            $table->foreign('id_pedido_cliente')->references('id_pedido_cliente')->on('Pedido_cliente');

            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Venta');
    }
};
