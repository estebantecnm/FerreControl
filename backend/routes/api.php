<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Detalle_pedido_proveedorController;
use App\Http\Controllers\Detalle_pedidoController;
use App\Http\Controllers\Detalle_ventaController;
use App\Http\Controllers\EspecificacionController;
use App\Http\Controllers\Movimiento_stockController;
use App\Http\Controllers\Pedido_clienteController;
use App\Http\Controllers\Pedido_proveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\RolController;

use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AuthController;


// Ruta pública para entrar
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (Necesitan el Token que da el login)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // APIs de cada modelo
    //CRUDs

    // API de cliente
    Route::get('/clientes', [ClienteController::class, 'index']);//funciona
    Route::post('/clientes', [ClienteController::class, 'store']);//funciona
    Route::get('/clientes/{id}', [ClienteController::class, 'show']);//funciona
    Route::put('/clientes/{id}', [ClienteController::class, 'update']);//funciona
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);

    // API de proveedor
    Route::get('/proveedores', [ProveedorController::class, 'index']);//funciona
    Route::post('/proveedores', [ProveedorController::class, 'store']);//funciona
    Route::get('/proveedores/{id}', [ProveedorController::class, 'show']);//funciona
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update']);//funciona
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy']);

    // API de producto
    Route::get('/productos', [ProductoController::class, 'index']);//funciona
    Route::post('/productos', [ProductoController::class, 'store']);//funciona
    Route::get('/productos/{id}', [ProductoController::class, 'show']);//funciona
    Route::put('/productos/{id}', [ProductoController::class, 'update']);//funcioa
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

    // API de categoría
    Route::get('/categorias', [CategoriaController::class, 'index']);//funciona
    Route::post('/categorias', [CategoriaController::class, 'store']);//funciona
    Route::get('/categorias/{id}', [CategoriaController::class, 'show']);//funcioa
    Route::put('/categorias/{id}', [CategoriaController::class, 'update']);//funciona
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

    // API de especificación
    Route::get('/especificaciones', [EspecificacionController::class, 'index']);//funciona
    Route::post('/especificaciones', [EspecificacionController::class, 'store']);//funciona
    Route::get('/especificaciones/{id}', [EspecificacionController::class, 'show']);//funciona
    Route::put('/especificaciones/{id}', [EspecificacionController::class, 'update']);//funciona
    Route::delete('/especificaciones/{id}', [EspecificacionController::class, 'destroy']);

    // API de usuario
    Route::get('/usuarios', [UsuarioController::class, 'index']);//funciona
    Route::post('/usuarios', [UsuarioController::class, 'store']);//funciona
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);//funciona
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);//funcion
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

    // API de rol
    Route::get('/roles', [RolController::class, 'index']);//funciona
    Route::post('/roles', [RolController::class, 'store']);//funciona
    Route::get('/roles/{id}', [RolController::class, 'show']);//funciona
    Route::put('/roles/{id}', [RolController::class, 'update']);//funcoa
    Route::delete('/roles/{id}', [RolController::class, 'destroy']);

    // API de pedido cliente
    Route::get('/pedidos-cliente', [Pedido_clienteController::class, 'index']);//funciona
    //Route::post('/pedidos-cliente', [Pedido_clienteController::class, 'store']);//funciona
    Route::get('/pedidos-cliente/{id}', [Pedido_clienteController::class, 'show']);//funciona
    Route::put('/pedidos-cliente/{id}', [Pedido_clienteController::class, 'update']);//funciona
    Route::delete('/pedidos-cliente/{id}', [Pedido_clienteController::class, 'destroy']);

    // API de detalle pedido del cliente
    /*
    Route::get('/detalles-pedido', [Detalle_pedidoController::class, 'index']);//funciona
    Route::post('/detalles-pedido', [Detalle_pedidoController::class, 'store']);//funciona
    Route::get('/detalles-pedido/{id}', [Detalle_pedidoController::class, 'show']);//funciona
    Route::put('/detalles-pedido/{id}', [Detalle_pedidoController::class, 'update']);//funciona
    Route::delete('/detalles-pedido/{id}', [Detalle_pedidoController::class, 'destroy']);
    */

    // API de pedido proveedor
    Route::get('/pedidos-proveedor', [Pedido_proveedorController::class, 'index']);//funciona
    //Route::post('/pedidos-proveedor', [Pedido_proveedorController::class, 'store']);//funciona
    Route::get('/pedidos-proveedor/{id}', [Pedido_proveedorController::class, 'show']);//funciona
    Route::put('/pedidos-proveedor/{id}', [Pedido_proveedorController::class, 'update']);//funciona
    Route::delete('/pedidos-proveedor/{id}', [Pedido_proveedorController::class, 'destroy']);

    // API de detalle pedido proveedor
    /*
    Route::get('/detalles-pedido-proveedor', [Detalle_pedido_proveedorController::class, 'index']);//funciona
    Route::post('/detalles-pedido-proveedor', [Detalle_pedido_proveedorController::class, 'store']);//funciona
    Route::get('/detalles-pedido-proveedor/{id}', [Detalle_pedido_proveedorController::class, 'show']);//funciona
    Route::put('/detalles-pedido-proveedor/{id}', [Detalle_pedido_proveedorController::class, 'update']);//funciona
    Route::delete('/detalles-pedido-proveedor/{id}', [Detalle_pedido_proveedorController::class, 'destroy']);
    */

    // API de ventas
    Route::get('/ventas', [VentaController::class, 'index']);//funciona
    //Route::post('/ventas', [VentaController::class, 'store']);//funciona
    Route::get('/ventas/{id}', [VentaController::class, 'show']);//funciona
    Route::put('/ventas/{id}', [VentaController::class, 'update']);//funciona
    Route::delete('/ventas/{id}', [VentaController::class, 'destroy']);

    // API de detalle venta
    /*
    Route::get('/detalles-venta', [Detalle_ventaController::class, 'index']);//funciona
    Route::post('/detalles-venta', [Detalle_ventaController::class, 'store']);//funciona
    Route::get('/detalles-venta/{id}', [Detalle_ventaController::class, 'show']);//funciona
    Route::put('/detalles-venta/{id}', [Detalle_ventaController::class, 'update']);//funciona
    Route::delete('/detalles-venta/{id}', [Detalle_ventaController::class, 'destroy']);
    */

    // API de movimiento stock
    Route::get('/movimientos-stock', [Movimiento_stockController::class, 'index']);//   funciona
    Route::post('/movimientos-stock', [Movimiento_stockController::class, 'store']);//funciona
    Route::get('/movimientos-stock/{id}', [Movimiento_stockController::class, 'show']);//funciona
    Route::put('/movimientos-stock/{id}', [Movimiento_stockController::class, 'update']);//funciona
    Route::delete('/movimientos-stock/{id}', [Movimiento_stockController::class, 'destroy']);

    // Reportes de Inventario
    Route::get('/reportes/stock-critico', [ReporteController::class, 'stockCritico']);

    // Reporte de Ganancias
    Route::get('/reportes/ganancias', [ReporteController::class, 'reporteGanancias']);

    //API LOGICA DE NEGOCIO, FUNCIONAN 

    // 1. Pedidos de Clientes (La "Cotización")
    Route::post('/pedidos-cliente', [Pedido_clienteController::class, 'store']);
    // 2. Ventas (Aquí se procesa el pago del pedido y se baja el stock)
    Route::post('/ventas', [VentaController::class, 'store']);

    // 1. Pedidos a Proveedor
    Route::post('/pedidos-proveedor', [Pedido_proveedorController::class, 'store']);
    // Procesar la entrada de mercancía (cambiado el estado del pedido_proveedor a Entregado y sube el stock)
    Route::put('/pedidos-proveedor/{id}/recibir', [Pedido_proveedorController::class, 'recibir']);

});