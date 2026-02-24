<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::all();

        if ($ventas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron ventas'], 404);
        }

        return response()->json($ventas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_venta' => 'sometimes|required|date',
            'total_venta' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string|max:20',
            'pago_cliente' => 'required|integer|min:0',
            'cambio' => 'required|numeric|min:0',
            'id_cliente' => 'required|exists:Cliente,id_cliente',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $venta = Venta::create($request->all());

        return response()->json($venta, 201);
    }

    public function show($id)
    {
        $venta = Venta::find($id);

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        return response()->json($venta);
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
            'metodo_pago' => 'sometimes|required|string|max:20',
            'pago_cliente' => 'sometimes|required|integer|min:0',
            'cambio' => 'sometimes|required|numeric|min:0',
            'id_cliente' => 'sometimes|required|exists:Cliente,id_cliente',
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