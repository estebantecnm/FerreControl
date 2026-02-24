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
        Schema::create('Pedido_cliente', function (Blueprint $table) {
            $table->increments('id_pedido_cliente'); // SERIAL PK
            $table->date('fecha_pedido');
            $table->float('total');
            $table->float('impuesto');
            $table->string('estado', 20);
            $table->string('tipo_pedido', 25);
            
            // Llave foránea al Usuario (quien realizó el pedido)
            $table->unsignedInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('Usuario');

            // NUEVA RELACIÓN: Con el Cliente (quien realiza el pedido)
            $table->unsignedInteger('id_cliente');
            $table->foreign('id_cliente')->references('id_cliente')->on('Cliente');
            $table->timestamps(); // Esto crea created_at y updated_at
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Pedido_cliente');
    }
};
