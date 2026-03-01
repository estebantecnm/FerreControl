<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Detalle_venta;
use App\Models\Pedido_cliente;
use App\Models\Movimiento_stock;


class VentaController extends Controller
{
    public function index()
    {
        // Solo traemos el resumen de la venta para el listado
        $ventas = Venta::with([
                'cliente:id_cliente,nombre,ap_paterno', // Para saber a quién se le vendió
                'pedidoCliente:id_pedido_cliente,tipo_pedido' // Referencia al origen
            ])
            ->select('id_venta', 'fecha_venta', 'total_venta', 'metodo_pago', 'id_cliente', 'id_pedido_cliente')
            ->orderBy('fecha_venta', 'desc')
            ->get();

        if ($ventas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron ventas'], 404);
        }

        return response()->json($ventas);
    }

    /**
     * TRANSFORMAR PEDIDO EN VENTA
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_pedido_cliente' => 'required|exists:Pedido_cliente,id_pedido_cliente',
            'id_usuario' => 'required|exists:Usuario,id_usuario',
            'metodo_pago' => 'required|string|in:Efectivo,Tarjeta,Transferencia',
            'pago_cliente' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 422);

        return DB::transaction(function () use ($request) {
            $pedido = Pedido_cliente::with('detalles')->findOrFail($request->id_pedido_cliente);

            if ($pedido->estado === 'Pagado') {
                return response()->json(["error" => "Este pedido ya fue pagado."], 400);
            }

            // 1. Crear la Venta
            $venta = Venta::create([
                'fecha_venta' => now(),
                'total_venta' => $pedido->total,
                'metodo_pago' => $request->metodo_pago,
                'pago_cliente' => $request->pago_cliente,
                'cambio' => $request->pago_cliente - $pedido->total,
                'id_cliente' => $pedido->id_cliente,
                'id_pedido_cliente' => $pedido->id_pedido_cliente
            ]);

            // 2. Procesar Detalles y Movimientos
            foreach ($pedido->detalles as $item) {
                // CALCULAMOS EL STOCK ACTUAL basado en el historial de movimientos
                // Sumamos Entradas - Salidas de la tabla Movimiento_stock
                $stockAnterior = Movimiento_stock::where('id_producto', $item->id_producto)
                    ->selectRaw("SUM(CASE WHEN tipo_movimiento = 'Entrada' THEN cantidad ELSE -cantidad END) as total")
                    ->value('total') ?? 0;

                // Validar si hay suficiente
                if ($stockAnterior < $item->cantidad) {
                    throw new \Exception("Stock insuficiente. Disponible: {$stockAnterior}, Requerido: {$item->cantidad}");
                }

                $stockNuevo = $stockAnterior - $item->cantidad;

                // A. Crear detalle de venta
                Detalle_venta::create([
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $item->id_producto,
                ]);

                // B. REGISTRAR EL MOVIMIENTO (Aquí es donde "vive" el stock ahora)
                Movimiento_stock::create([
                    'tipo_movimiento' => 'Salida',
                    'cantidad' => $item->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'id_producto' => $item->id_producto,
                    'id_usuario' => $request->id_usuario,
                ]);
            }

            $pedido->update(['estado' => 'Pagado']);

            return response()->json([
                'msj' => 'Venta procesada. El stock se actualizó en el historial de movimientos.',
                'venta' => $venta->load('detalles')
            ], 201);
        });
    }

    public function show($id)
    {
        $venta = Venta::with(['detalles.producto', 'cliente', 'pedidoCliente'])->find($id);
        return $venta ? response()->json($venta) : response()->json(['message' => 'Venta no encontrada'], 404);
    }

    public function update(Request $request, $id)
    {
        $venta = Venta::find($id);

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fecha_venta' => 'sometimes|required|date',
            'total_venta' => 'sometimes|required|numeric|min:0',
            'metodo_pago' => 'sometimes|required|string|max:20|in:Efectivo,Tarjeta,Transferencia',
            'pago_cliente' => 'sometimes|required|integer|min:0',
            'cambio' => 'sometimes|required|numeric|min:0',
            'id_cliente' => 'sometimes|required|exists:Cliente,id_cliente',
            'id_pedido_cliente' => 'sometimes|required|exists:Pedido_cliente,id_pedido_cliente',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $venta->update($request->all());

        return response()->json($venta);
    }

    public function destroy($id)
    {
        $venta = Venta::find($id);

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        $venta->delete();

        return response()->json(['message' => 'Venta eliminada correctamente']);
    }
}