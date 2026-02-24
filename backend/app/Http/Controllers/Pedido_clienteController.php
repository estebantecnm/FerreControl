<?php

namespace App\Http\Controllers;

use App\Models\Pedido_cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class Pedido_clienteController extends Controller
{
    public function index()
    {
        $pedidos = Pedido_cliente::all();

        if ($pedidos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron pedidos de cliente'], 404);
        }

        return response()->json($pedidos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_pedido' => 'sometimes|date', 
            'total' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20',
            'tipo_pedido' => 'required|string|max:25',
            'id_usuario' => 'required|exists:Usuario,id_usuario',
            'id_cliente' => 'required|exists:Cliente,id_cliente',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pedido = Pedido_cliente::create($request->all());

        return response()->json($pedido, 201);
    }

    public function show($id)
    {
        $pedido = Pedido_cliente::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de cliente no encontrado'], 404);
        }

        return response()->json($pedido);
    }

    public function update(Request $request, $id)
    {
        $pedido = Pedido_cliente::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de cliente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fecha_pedido' => 'sometimes|required|date', 
            'total' => 'sometimes|required|numeric|min:0',
            'impuesto' => 'sometimes|required|numeric|min:0',
            'estado' => 'sometimes|required|string|max:20',
            'tipo_pedido' => 'sometimes|required|string|max:25',
            'id_usuario' => 'sometimes|required|exists:Usuario,id_usuario',
            'id_cliente' => 'sometimes|required|exists:Cliente,id_cliente',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pedido->update($request->all());

        return response()->json($pedido);
    }

    public function destroy($id)
    {
        $pedido = Pedido_cliente::find($id);

        if (!$pedido) {
            return response()->json(['message' => 'Pedido de cliente no encontrado'], 404);
        }

        $pedido->delete();

        return response()->json(['message' => 'Pedido de cliente eliminado correctamente']);
    }
}