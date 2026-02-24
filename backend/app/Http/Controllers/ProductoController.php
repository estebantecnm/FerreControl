<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::all();

        if ($productos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron productos'], 404);
        }

        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string', 
            'marca' => 'required|string|max:25',
            'precio_venta' => 'required|numeric',
            'precio_compra' => 'required|numeric',
            'utilidad' => 'required|numeric',
            'codigo_barras' => 'required|string',
            'status' => 'required|string|max:20',
            'unidad_medida' => 'required|string|max:25',
            'cantidad_presentacion' => 'required|integer',
            'color' => 'sometimes|string|max:20',
            'id_categoria' => 'required|exists:Categoria,id_categoria',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $producto = Producto::create($request->all());

        return response()->json($producto, 201);
    }

    public function show($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string', 
            'marca' => 'sometimes|required|string|max:25',
            'precio_venta' => 'sometimes|required|numeric',
            'precio_compra' => 'sometimes|required|numeric',
            'utilidad' => 'sometimes|required|numeric',
            'codigo_barras' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|max:20',
            'unidad_medida' => 'sometimes|required|string|max:25',
            'cantidad_presentacion' => 'sometimes|required|integer',
            'color' => 'sometimes|string|max:20',
            'id_categoria' => 'sometimes|required|exists:Categoria,id_categoria',
         
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $producto->update($request->all());

        return response()->json($producto);
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}