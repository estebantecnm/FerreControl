<?php

namespace App\Http\Controllers;

use App\Models\Detalle_pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class Detalle_pedidoController extends Controller
{
    public function index()
    {
        $detalles = Detalle_pedido::all();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles de pedido'], 404);
        }

        return response()->json($detalles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'id_pedido_cliente' => 'required|exists:Pedido_cliente,id_pedido_cliente',
            'id_producto' => 'required|exists:Producto,id_producto',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detalle = Detalle_pedido::create($request->all());

        return response()->json($detalle, 201);
    }

    public function show($id)
    {
        $detalle = Detalle_pedido::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }

        return response()->json($detalle);
    }

    public function update(Request $request, $id)
    {
        $detalle = Detalle_pedido::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cantidad' => 'sometimes|required|integer|min:1',
            'precio_unitario' => 'sometimes|required|numeric|min:0',
            'id_pedido_cliente' => 'sometimes|required|exists:Pedido_cliente,id_pedido_cliente',
            'id_producto' => 'sometimes|required|exists:Producto,id_producto',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detalle->update($request->all());

        return response()->json($detalle);
    }

    public function destroy($id)
    {
        $detalle = Detalle_pedido::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }

        $detalle->delete();

        return response()->json(['message' => 'Detalle de pedido eliminado correctamente']);
    }
}