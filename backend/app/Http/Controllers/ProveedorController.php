<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all();

        if ($proveedores->isEmpty()) {
            return response()->json(['message' => 'No se encontraron proveedores'], 404);
        }

        return response()->json($proveedores);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_contacto' => 'required|string|max:30',
            'telefono_contacto' => 'required|string|max:10',
            'correo_contacto' => 'required|string|max:64',
            'nombre' => 'required|string|max:50',
            'telefono' => 'required|string|max:10',
            'tiempo_entrega' => 'required|string|max:20',
            'correo' => 'required|string|max:64',
            'tipo' => 'required|string|max:20',
            'rfc' => 'required|string|max:13',
            'num_int' => 'required|integer',
            'num_ext' => 'required|integer',
            'pais' => 'required|string|max:30',
            'estado' => 'required|string|max:30',
            'municipio' => 'required|string|max:30',
            'ciudad' => 'required|string|max:30',
            'colonia' => 'required|string|max:30',
            'calle' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $proveedor = Proveedor::create($request->all());

        return response()->json($proveedor, 201);
    }

    public function show($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        return response()->json($proveedor);
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_contacto' => 'sometimes|required|string|max:30',
            'telefono_contacto' => 'sometimes|required|string|max:10',
            'correo_contacto' => 'sometimes|required|string|max:64',
            'nombre' => 'sometimes|required|string|max:50',
            'telefono' => 'sometimes|required|string|max:10',
            'tiempo_entrega' => 'sometimes|required|string|max:20',
            'correo' => 'sometimes|required|string|max:64',
            'tipo' => 'sometimes|required|string|max:20',
            'rfc' => 'sometimes|required|string|max:13',
            'num_int' => 'sometimes|required|integer',
            'num_ext' => 'sometimes|required|integer',
            'pais' => 'sometimes|required|string|max:30',
            'estado' => 'sometimes|required|string|max:30',
            'municipio' => 'sometimes|required|string|max:30',
            'ciudad' => 'sometimes|required|string|max:30',
            'colonia' => 'sometimes|required|string|max:30',
            'calle' => 'sometimes|required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $proveedor->update($request->all());

        return response()->json($proveedor);
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $proveedor->delete();

        return response()->json(['message' => 'Proveedor eliminado correctamente']);
    }
}