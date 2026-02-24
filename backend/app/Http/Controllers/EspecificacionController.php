<?php

namespace App\Http\Controllers;

use App\Models\Especificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class EspecificacionController extends Controller
{
    public function index()
    {
        $especificaciones = Especificacion::all();

        if ($especificaciones->isEmpty()) {
            return response()->json(['message' => 'No se encontraron especificaciones'], 404);
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