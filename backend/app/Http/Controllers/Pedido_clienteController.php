<?php

namespace App\Http\Controllers;

use App\Models\Pedido_cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Pedido_clienteController extends Controller
{
    public function index()
    {
        // Solo traemos lo esencial para la tabla principal
        $pedidos = Pedido_cliente::with([
                'cliente:id_cliente,nombre,ap_paterno', // Solo nombre para identificarlo
                'usuario:id_usuario,nombre'            // Quién hizo la nota
            ])
            ->select('id_pedido_cliente', 'fecha_pedido', 'total', 'estado', 'id_cliente', 'id_usuario')
            ->orderBy('fecha_pedido', 'desc')
            ->get();

        if ($pedidos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron pedidos'], 404);
        }

        return response()->json($pedidos);
    }

    /**
     * CREAR PEDIDO (COTIZACIÓN/NOTA PREVIA)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
            'estado' => 'sometimes|string|max:20|in:Pendiente,Pagado,Cancelado',
            'tipo_pedido' => 'required|string|max:25|in:Mostrador,Telefonico', // Ej: Mostrador, Telefónico
            'id_usuario' => 'required|exists:Usuario,id_usuario',
            'id_cliente' => 'required|exists:Cliente,id_cliente',
            // Validación de los productos que integran la nota
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:Producto,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return DB::transaction(function () use ($request) {
            // 1. Crear el Pedido (Encabezado)
            // Forzamos el estado a 'Pendiente' porque aún no se ha convertido en Venta
            $pedido = Pedido_cliente::create([
                'fecha_pedido' => now(), 
                'total' => $request->total,
                'impuesto' => $request->impuesto,
                'estado' => 'Pendiente', 
                'tipo_pedido' => $request->tipo_pedido,
                'id_usuario' => $request->id_usuario,
                'id_cliente' => $request->id_cliente,
            ]);

            // 2. Crear los detalles del pedido
            foreach ($request->productos as $item) {
                DB::table('Detalle_pedido')->insert([
                    'id_pedido_cliente' => $pedido->id_pedido_cliente,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'msj' => 'Pedido generado correctamente (Cotización)',
                'pedido' => $pedido->load('detalles')
            ], 201);
        });
    }

    public function show($id)
    {
        // Incluimos los productos para ver qué contiene la nota
        $pedido = Pedido_cliente::with(['detalles.producto','cliente', 'usuario'])->find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de cliente no encontrado'], 404);
        }

        return response()->json($pedido);
    }

    public function update(Request $request, $id)
    {
        $pedido = Pedido_cliente::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de cliente no encontrado'], 404);
        }

        // Si el pedido ya está pagado, no debería poder editarse
        if ($pedido->estado === 'Pagado') {
            return response()->json(['message' => 'No se puede editar un pedido que ya fue pagado'], 400);
        }

        $validator = Validator::make($request->all(), [
            'total' => 'sometimes|required|numeric|min:0',
            'impuesto' => 'sometimes|required|numeric|min:0',
            'estado' => 'sometimes|required|string|in:Pendiente,Cancelado,Pagado',
            'tipo_pedido' => 'sometimes|required|string|max:25|in:Mostrador,Telefonico',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pedido->update($request->all());

        return response()->json($pedido);
    }

    public function destroy($id)
    {
        $pedido = Pedido_cliente::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de cliente no encontrado'], 404);
        }

        // Regla de negocio: Si ya se generó una venta de esto, no se puede borrar el pedido
        if ($pedido->estado === 'Pagado') {
            return response()->json(['message' => 'No se puede eliminar un pedido que ya fue procesado en una venta'], 400);
        }

        $pedido->delete();

        return response()->json(['message' => 'Pedido eliminado correctamente']);
    }
}