<?php

namespace App\Http\Controllers;

use App\Models\Especificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class EspecificacionController extends Controller
{
    public function index(Request $request) // Añadimos el Request
    {
        // Verificamos si enviaron un id_producto en la URL (?id_producto=X)
        $id_producto = $request->query('id_producto');

        if ($id_producto) {
            // Buscamos solo las que pertenecen a ese producto
            $especificaciones = Especificacion::where('id_producto', $id_producto)->get();
        } else {
            // Comportamiento original: devolver todas
            $especificaciones = Especificacion::all();
        }

        if ($especificaciones->isEmpty()) {
            // Es mejor devolver un array vacío [] con 200 en lugar de 404
            // para que el frontend no lo detecte como "error de servidor"
            return response()->json([], 200);
        }

        return response()->json($especificaciones);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_atributo' => 'sometimes|string|max:30', 
            'valor' => 'sometimes|string|max:20',
            'id_producto' => 'required|exists:Producto,id_producto',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $especificacion = Especificacion::create($request->all());

        return response()->json($especificacion, 201);
    }

    public function show($id)
    {
        $especificacion = Especificacion::find($id);

        if (!$especificacion) {
            return response()->json(['message' => 'Especificación no encontrada'], 404);
        }

        return response()->json($especificacion);
    }

    public function update(Request $request, $id)
    {
        $especificacion = Especificacion::find($id);

        if (!$especificacion) {
            return response()->json(['message' => 'Especificación no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_atributo' => 'sometimes|string|max:30', 
            'valor' => 'sometimes|string|max:20',
            'id_producto' => 'sometimes|exists:Producto,id_producto',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $especificacion->update($request->all());

        return response()->json($especificacion);
    }

    public function destroy($id)
    {
        $especificacion = Especificacion::find($id);

        if (!$especificacion) {
            return response()->json(['message' => 'Especificación no encontrada'], 404);
        }

        $especificacion->delete();

        return response()->json(['message' => 'Especificación eliminada correctamente']);
    }
}