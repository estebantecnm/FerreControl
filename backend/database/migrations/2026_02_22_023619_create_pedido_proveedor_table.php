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
        Schema::create('Pedido_proveedor', function (Blueprint $table) {
            $table->increments('id_pedido');
            $table->integer('folio')->unique();
            $table->date('fecha_pedido');
            $table->date('fecha_entrega');
            $table->date('fecha_recepcion');
            $table->float('total');
            $table->string('estado', 20);
            $table->string('condiciones', 25);
            $table->string('notas', 50)->nullable();

            $table->unsignedInteger('id_proveedor');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('Proveedor');

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
        Schema::dropIfExists('Pedido_proveedor');
    }
};
