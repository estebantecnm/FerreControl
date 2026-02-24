<?php

namespace App\Http\Controllers;

use App\Models\Detalle_venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class Detalle_ventaController extends Controller
{
    public function index()
    {
        $detalles = Detalle_venta::all();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles de venta'], 404);
        }

        return response()->json($detalles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'id_venta' => 'required|exists:Venta,id_venta',
            'id_producto' => 'required|exists:Producto,id_producto',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $detalle = Detalle_venta::create($request->all());

        return response()->json($detalle, 201);
    }

    public function show($id)
    {
        $detalle = Detalle_venta::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de venta no encontrado'], 404);
        }

        return response()->json($detalle);
    }

    public function update(Request $request, $id)
    {
        $detalle = Detalle_venta::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de venta no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cantidad' => 'sometimes|required|integer|min:1',
            'precio_unitario' => 'sometimes|required|numeric|min:0',
            'id_venta' => 'sometimes|required|exists:Venta,id_venta',
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
        $detalle = Detalle_venta::find($id);

        if (!$detalle) {
            return response()->json(['message' => 'Detalle de venta no encontrado'], 404);
        }

        $detalle->delete();

        return response()->json(['message' => 'Detalle de venta eliminado correctamente']);
    }
}