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
    Schema::create('Detalle_pedido_proveedor', function (Blueprint $table) {
        $table->increments('id_detalle_pedido_prov');
        $table->float('precio_compra');
        $table->integer('cantidad');
        
        $table->unsignedInteger('id_pedido');
        $table->unsignedInteger('id_producto'); // Nombre exacto de tu DDL
        
        $table->foreign('id_pedido')->references('id_pedido')->on('Pedido_proveedor');
        $table->foreign('id_producto')->references('id_producto')->on('Producto');
        $table->timestamps(); // Esto crea created_at y updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Detalle_pedido_proveedor');
    }
};
