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
    // 1. Quitamos 'required' de id_usuario para que no falle si la vista no lo envía
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
        'cantidad_inicial' => 'nullable|integer|min:0',
        'id_usuario' => 'nullable|exists:Usuario,id_usuario', // Cambiado a nullable
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    return DB::transaction(function () use ($request) {
        $data = $request->all();
        
        // 2. Si la vista no envía id_usuario, le ponemos el ID 1 (Admin) por defecto
        if (!$request->has('id_usuario')) {
            $data['id_usuario'] = 1; 
        }

        $producto = Producto::create($data);

        // Lógica de Movimiento de stock...
        $cantidadInicial = $request->input('cantidad_inicial', 0);
        if ($cantidadInicial > 0) {
            Movimiento_stock::create([
                'tipo_movimiento' => 'Entrada',
                'cantidad' => $cantidadInicial,
                'stock_anterior' => 0,
                'stock_nuevo' => $cantidadInicial,
                'id_producto' => $producto->id_producto,
                'id_usuario' => $data['id_usuario'], // Usamos el ID asignado
            ]);
        }

        return response()->json(['producto' => $producto], 201);
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
        'nombre' => 'sometimes|required|string|max:50', 
        'marca' => 'sometimes|required|string|max:25',
        'precio_venta' => 'sometimes|required|numeric',
        'precio_compra' => 'sometimes|required|numeric',
        'utilidad' => 'sometimes|required|numeric',
        // REGLA CRÍTICA: unique:Tabla,Columna,ID_a_ignorar,Nombre_Columna_ID
        'codigo_barras' => 'sometimes|required|numeric|unique:Producto,codigo_barras,' . $id . ',id_producto',
        'status' => 'sometimes|required|string|max:20',
        'unidad_medida' => 'sometimes|required|string|max:25',
        'cantidad_presentacion' => 'sometimes|required|integer',
        'color' => 'sometimes|nullable|string|max:20', 
        'id_categoria' => 'sometimes|required|exists:Categoria,id_categoria',
        'id_usuario' => 'nullable|exists:Usuario,id_usuario'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $data = $request->all();
    
    // Aseguramos el id_usuario para que PostgreSQL no proteste
    if (!isset($data['id_usuario'])) {
        $data['id_usuario'] = $producto->id_usuario ?? 1;
    }

    $producto->update($data);

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
