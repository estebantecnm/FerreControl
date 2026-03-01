<?php

namespace App\Http\Controllers;

use App\Models\Pedido_proveedor;
use App\Models\Producto;
use App\Models\Movimiento_stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Importante para transacciones
use App\Http\Controllers\Controller;
use App\Models\Detalle_pedido_proveedor;


class Pedido_proveedorController extends Controller
{
    public function index()
    {
        // Solo cargamos el nombre del proveedor para la tabla principal
        $pedidos = Pedido_proveedor::with('proveedor:id_proveedor,nombre')
            ->select('id_pedido', 'folio', 'fecha_pedido', 'total', 'estado', 'id_proveedor')
            ->get();
        if ($pedidos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron pedidos'], 404);
        }

        return response()->json($pedidos);
    }
    // Los métodos show, update y destroy pueden permanecer similares, 
    // pero con cuidado de no alterar stock en update/destroy sin lógica extra.
    public function show($id)
    {
        //Se muestra el detalle con sus productos para ver qué se pidió al proveedor, ademas de la info del proveedor y usuario que hizo el pedido
        $pedido = Pedido_proveedor::with('detalles.producto','proveedor', 'usuario')->find($id);
        return $pedido ? response()->json($pedido) : response()->json(['msj' => 'No encontrado'], 404);
    }

    /**
     * PASO 1: Crear el pedido con estado "Pendiente" y sus detalles
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folio' => 'required|int|unique:Pedido_proveedor,folio', 
            'fecha_pedido' => 'required|date',
            'fecha_entrega' => 'required|date',
            'fecha_recepcion' => 'sometimes|date',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20|in:Pendiente,Recibido,Cancelado',
            'condiciones' => 'sometimes|string|max:25',
            'notas' => 'sometimes|string|max:50',
            'id_proveedor' => 'required|exists:Proveedor,id_proveedor',
            'id_usuario' => 'required|exists:Usuario,id_usuario',
            
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:Producto,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_compra' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 422);

        return DB::transaction(function () use ($request) {
            // Crea el pedido en Pendiente (No toca stock aún)
            $pedido = Pedido_proveedor::create([
                'folio' => $request->folio,
                'fecha_pedido' => $request->fecha_pedido,
                'fecha_entrega' => $request->fecha_entrega,
                'total' => $request->total,
                'estado' => 'Pendiente',
                'id_proveedor' => $request->id_proveedor,
                'id_usuario' => $request->id_usuario,
                'condiciones' => $request->condiciones,
                'notas' => $request->notas,
            ]);
                // Crea los detalles del pedido
            foreach ($request->productos as $item) {
                DB::table('Detalle_pedido_proveedor')->insert([
                    'precio_compra' => $item['precio_compra'],
                    'cantidad' => $item['cantidad'],
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $item['id_producto'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['msj' => 'Pedido a proveedor registrado (Pendiente)', 'pedido' => $pedido], 201);
        });
    }

    /**
     * PASO 2 y 3: Función para recibir la mercancía y subir el stock
     */
    public function recibir($id = null, Request $request)
    {
        $pedido = Pedido_proveedor::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        // Validación de negocio: No recibir un pedido que ya fue completado o cancelado
        if ($pedido->estado !== 'Pendiente') {
            return response()->json(['message' => 'Este pedido ya fue procesado o no está pendiente'], 400);
        }

        return DB::transaction(function () use ($pedido, $request) {
            // 1. Cambiar estado y registrar fecha de recepción
            $pedido->update([
                'estado' => 'Recibido',
                'fecha_recepcion' => now(),
                'notas' => $request->notas ?? 'Mercancía recibida correctamente'
            ]);

            // 2. Obtener los productos de este pedido (usando el modelo de detalle)
            // Asumiendo que la relación se llama 'detalles' en el modelo Pedido_proveedor
            $detalles = DB::table('Detalle_pedido_proveedor')
                          ->where('id_pedido', $pedido->id_pedido)
                          ->get();

            foreach ($detalles as $detalle) {
                // 3. CALCULAR STOCK DINÁMICO (Suma Entradas - Salidas)
                $stockAnterior = Movimiento_stock::where('id_producto', $detalle->id_producto)
                    ->selectRaw("SUM(CASE WHEN tipo_movimiento = 'Entrada' THEN cantidad ELSE -cantidad END) as total")
                    ->value('total') ?? 0;

                $stockNuevo = $stockAnterior + $detalle->cantidad;

                // 4. Registrar el Movimiento (Ahora con todos los campos del fillable)
                Movimiento_stock::create([
                    'tipo_movimiento'  => 'Entrada',
                    'cantidad'         => $detalle->cantidad,
                    'stock_anterior'   => $stockAnterior,
                    'stock_nuevo'      => $stockNuevo,
                    'id_producto'      => $detalle->id_producto,
                    'id_usuario'       => $pedido->id_usuario // El usuario que creó el pedido
                ]);
            }

            return response()->json([
                'message' => 'Mercancía recibida. El stock ha sido actualizado.',
                'pedido' => $pedido->load('detalles')
            ]);
        });
    }


}