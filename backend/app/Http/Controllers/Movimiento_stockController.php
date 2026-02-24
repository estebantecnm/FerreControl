<?php

namespace App\Http\Controllers;

use App\Models\Movimiento_stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class Movimiento_stockController extends Controller
{
    public function index()
    {
        $movimientos = Movimiento_stock::all();

        if ($movimientos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron movimientos de stock'], 404);
        }

        return response()->json($movimientos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_movimiento' => 'required|string|max:30',
            'cantidad' => 'required|integer|min:1',
            'stock_anterior' => 'nullable|integer',
            'stock_nuevo' => 'nullable|integer',
            'id_producto' => 'required|exists:Producto,id_producto',
            'id_usuario' => 'required|exists:Usuario,id_usuario',
        ]);

        $movimiento = Movimiento_stock::create($validated);
        return response()->json($movimiento, 201);
    }

    public function show($id)
    {
        $movimiento = Movimiento_stock::findOrFail($id);
        return response()->json($movimiento);
    }

    public function update(Request $request, $id)
    {
        $movimiento = Movimiento_stock::findOrFail($id);
        
        $validated = $request->validate([
            'tipo_movimiento' => 'sometimes|string|max:30',
            'cantidad' => 'sometimes|integer|min:1',
            'stock_anterior' => 'nullable|integer',
            'stock_nuevo' => 'nullable|integer',
            'id_producto' => 'sometimes|exists:Producto,id_producto',
            'id_usuario' => 'sometimes|exists:Usuario,id_usuario',
        ]);

        $movimiento->update($validated);
        return response()->json($movimiento);
    }

    public function destroy($id)
    {
        Movimiento_stock::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}