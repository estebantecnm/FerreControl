<?php

namespace App\Http\Controllers;

use App\Models\Detalle_pedido_proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class Detalle_pedido_proveedorController extends Controller
{
    public function index()
    {
        $detalles = Detalle_pedido_proveedor::all();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles de pedido proveedor'], 404);
        }

        return response()->json($detalles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'precio_compra' => 'required|numeric|min:0', 
            'cantidad' => 'required|integer|min:1',
            'id_pedido' => 'required|exists:Pedido_proveedor,id_pedido',
            'id_producto' => 'required|exists:Producto,id_producto',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detalle = Detalle_pedido_proveedor::create($request->all());

        return response()->json($detalle, 201);
    }

    public function show($id)
    {
        $detalle = Detalle_pedido_proveedor::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de pedido proveedor no encontrado'], 404);
        }

        return response()->json($detalle);
    }

    public function update(Request $request, $id)
    {
        $detalle = Detalle_pedido_proveedor::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de pedido proveedor no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'precio_compra' => 'sometimes|required|numeric|min:0',
            'cantidad' => 'sometimes|required|integer|min:1',
            'id_pedido' => 'sometimes|required|exists:Pedido_proveedor,id_pedido',
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
        $detalle = Detalle_pedido_proveedor::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de pedido proveedor no encontrado'], 404);
        }

        $detalle->delete();

        return response()->json(['message' => 'Detalle de pedido proveedor eliminado correctamente']);
    }
}