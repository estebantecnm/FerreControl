<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    /**
     * Mostrar lista de clientes
     */
    public function index()
    {
        $clientes = Cliente::all();

        if ($clientes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron clientes'], 404);
        }

        return response()->json($clientes);
    }

    /**
     * Crear nuevo cliente
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:25',
            'ap_paterno' => 'required|string|max:25',
            'ap_materno' => 'required|string|max:25',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:9',
            'correo' => 'required|string|max:64',
            'rfc' => 'required|string|max:13',
            'telefono' => 'required|string|max:10',
            'num_ext' => 'nullable|int',
            'num_int' => 'nullable|int',
            'calle' => 'required|string|max:30',
            'colonia' => 'required|string|max:30',
            'municipio' => 'required|string|max:30',
            'estado' => 'required|string|max:30',
            //status del cliente, activo o inactivo
            'status' => 'required|string|max:9|in:Activo,Inactivo',
            'limite_credito' => 'required|numeric|min:0',
            'saldo_pendiente' => 'required|numeric|min:0',
            'dias_credito' => 'required|integer|min:0',
            'tipo_cliente' => 'required|string|max:25|in:Fisica,Moral', // Fisica o Moral

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cliente = Cliente::create($request->all());

        return response()->json($cliente, 201);
    }

    /**
     * Mostrar cliente específico
     */
    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:25',
            'ap_paterno' => 'sometimes|string|max:25',
            'ap_materno' => 'sometimes|string|max:25',
            'fecha_nacimiento' => 'sometimes|date',
            'sexo' => 'sometimes|string|max:9',
            'correo' => 'sometimes|string|max:64',
            'rfc' => 'sometimes|string|max:13',
            'telefono' => 'sometimes|string|max:10',
            'num_ext' => 'nullable|int',
            'num_int' => 'nullable|int',
            'calle' => 'sometimes|string|max:30',
            'colonia' => 'sometimes|string|max:30',
            'municipio' => 'sometimes|string|max:30',
            'estado' => 'sometimes|string|max:30',
            'status' => 'sometimes|string|max:9|in:Activo,Inactivo',
            'limite_credito' => 'sometimes|numeric|min:0',
            'saldo_pendiente' => 'sometimes|numeric|min:0',
            'dias_credito' => 'sometimes|integer|min:0',
            'tipo_cliente' => 'sometimes|string|max:25|in:Fisica,Moral',
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cliente->update($request->all());

        return response()->json($cliente);
    }

    /**
     * Eliminar cliente
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->delete();

        return response()->json(['message' => 'Cliente eliminado correctamente']);
    }

    //consultar historial de compras de un cliente
    public function consultarHistorialCompras($id){
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        //recupera o accede a la tabla ventas y obtiene el historial de compras del cliente
        $historial = $cliente->ventas()->get();

        return response()->json($historial);
    }

    //obtener saldo pendiente de un cliente
    public function obtenerSaldoPendiente($id){
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        //recupera el saldo pendiente del cliente
        $saldoPendiente = $cliente->saldo_pendiente;

        return response()->json(['saldo_pendiente' => $saldoPendiente]);
    }

    //agregar saldo pendiente a un cliente
    public function agregarSaldoPendiente(Request $request, $id){
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'monto' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //agrega el monto al saldo pendiente del cliente
        $cliente->saldo_pendiente += $request->input('monto');
        $cliente->save();

        return response()->json(['message' => 'Saldo pendiente actualizado correctamente', 'saldo_pendiente' => $cliente->saldo_pendiente]);
    }


}