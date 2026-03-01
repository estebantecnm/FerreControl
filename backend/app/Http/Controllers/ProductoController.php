<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Movimiento_stock;
use App\Models\Categoria;
use App\Models\Usuario;

class ProductoController extends Controller
{
    public function index()
    {
        //Solo se muestra el producto, sin relaciones para optimizar la consulta
        $productos = Producto::all();

        return $productos->isEmpty() 
            ? response()->json(['message' => 'No se encontraron productos'], 404) 
            : response()->json($productos);
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
            // Atributos para la lógica de inventario inicial
            'cantidad_inicial' => 'nullable|integer|min:0',
            'id_usuario' => 'required|exists:Usuario,id_usuario', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return DB::transaction(function () use ($request) {
            // 1. Crear el Producto (Solo guardará lo que esté en $fillable del modelo)
            $producto = Producto::create($request->all());

            // 2. Si hay una cantidad inicial, registramos el Movimiento
            $cantidadInicial = $request->input('cantidad_inicial', 0);
            
            if ($cantidadInicial > 0) {
                Movimiento_stock::create([
                    'tipo_movimiento' => 'Entrada',
                    'cantidad' => $cantidadInicial,
                    'stock_anterior' => 0, // Por definición, un producto nuevo inicia en 0
                    'stock_nuevo' => $cantidadInicial,
                    'id_producto' => $producto->id_producto, // El ID recién generado
                    'id_usuario' => $request->id_usuario,
                ]);
            }

            return response()->json([
                'producto' => $producto
            ], 201);
        });
    }

    public function show($id)
    {
        //infraestructura para mostrar la categoria a la que pertenece el producto, ademas de sus especificaciones
        $producto = Producto::with(['categoria', 'especificaciones'])->find($id);

        return $producto 
            ? response()->json($producto) 
            : response()->json(['message' => 'Producto no encontrado'], 404);
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

        // Nota: En una ferretería real, a veces es mejor usar SoftDeletes
        // para no perder el historial de movimientos de stock.
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado correctamente']);
    }
}