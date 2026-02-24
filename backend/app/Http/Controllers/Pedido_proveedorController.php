<?php

namespace App\Http\Controllers;

use App\Models\Pedido_proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class Pedido_proveedorController extends Controller
{
    public function index()
    {
        $pedidos = Pedido_proveedor::all();

        if ($pedidos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron pedidos de proveedor'], 404);
        }

        return response()->json($pedidos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folio' => 'required|int', 
            'fecha_pedido' => 'required|date',
            'fecha_entrega' => 'required|date',
            'fecha_recepcion' => 'required|date',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20',
            'condiciones' => 'sometimes|string|max:25',
            'notas' => 'sometimes|string|max:50',
            'id_proveedor' => 'required|exists:Proveedor,id_proveedor',
            'id_usuario' => 'required|exists:Usuario,id_usuario',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pedido = Pedido_proveedor::create($request->all());

        return response()->json($pedido, 201);
    }

    public function show($id)
    {
        $pedido = Pedido_proveedor::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de proveedor no encontrado'], 404);
        }

        return response()->json($pedido);
    }

    public function update(Request $request, $id)
    {
        $pedido = Pedido_proveedor::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de proveedor no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'folio' => 'sometimes|required|int', 
            'fecha_pedido' => 'sometimes|required|date',
            'fecha_entrega' => 'sometimes|required|date',
            'fecha_recepcion' => 'sometimes|required|date',
            'total' => 'sometimes|required|numeric|min:0',
            'estado' => 'sometimes|required|string|max:20',
            'condiciones' => 'sometimes|string|max:25',
            'notas' => 'sometimes|string|max:50',
            'id_proveedor' => 'sometimes|required|exists:Proveedor,id_proveedor',
            'id_usuario' => 'sometimes|required|exists:Usuario,id_usuario',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pedido->update($request->all());

        return response()->json($pedido);
    }

    public function destroy($id)
    {
        $pedido = Pedido_proveedor::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de proveedor no encontrado'], 404);
        }

        $pedido->delete();

        return response()->json(['message' => 'Pedido de proveedor eliminado correctamente']);
    }
}