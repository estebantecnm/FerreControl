<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function stockCritico(Request $request)
    {
        // El usuario puede definir qué es "crítico" vía URL (ej: ?limite=5)
        // Por defecto usaremos 10 unidades.
        $limite = $request->query('limite', 10);

        $productosCriticos = DB::table('Producto as p')
            ->leftJoin('Movimiento_stock as m', 'p.id_producto', '=', 'm.id_producto')
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.marca',
                'p.codigo_barras',
                // La magia: Calculamos el balance actual (Entradas - Salidas)
                DB::raw("SUM(CASE WHEN m.tipo_movimiento = 'Entrada' THEN m.cantidad ELSE -m.cantidad END) as stock_actual")
            )
            ->groupBy('p.id_producto', 'p.nombre', 'p.marca', 'p.codigo_barras')
            // Solo queremos los que tengan menos del límite
            ->havingRaw("SUM(CASE WHEN m.tipo_movimiento = 'Entrada' THEN m.cantidad ELSE -m.cantidad END) <= ?", [$limite])
            ->get();

        if ($productosCriticos->isEmpty()) {
            return response()->json([
                'msj' => '¡Felicidades! Todo tu inventario está por encima del límite establecido.',
                'limite_consultado' => $limite,
                'data' => []
            ], 200);
        }

        return response()->json([
            'titulo' => 'Reporte de Productos con Bajo Inventario',
            'limite_critico' => $limite,
            'total_productos' => $productosCriticos->count(),
            'data' => $productosCriticos
        ]);
    }

    public function reporteGanancias(Request $request)
    {
        // 1. Definir el rango de fechas (Por defecto: Hoy)
        $fechaInicio = $request->query('desde', now()->format('Y-m-d') . ' 00:00:00');
        $fechaFin = $request->query('hasta', now()->format('Y-m-d') . ' 23:59:59');

        // 2. Consulta de Ventas con sus detalles
        $reporte = DB::table('Venta as v')
            ->join('Detalle_venta as dv', 'v.id_venta', '=', 'dv.id_venta')
            ->join('Producto as p', 'dv.id_producto', '=', 'p.id_producto')
            ->whereBetween('v.fecha_venta', [$fechaInicio, $fechaFin])
            ->select(
                DB::raw('SUM(dv.cantidad * dv.precio_unitario) as ingresos_totales'),
                // Calculamos el costo: cantidad vendida * precio al que compramos el producto
                DB::raw('SUM(dv.cantidad * p.precio_compra) as costo_total_mercancia'),
                // La utilidad es la diferencia
                DB::raw('SUM(dv.cantidad * (dv.precio_unitario - p.precio_compra)) as utilidad_neta')
            )
            ->first();

        // 3. Conteo de transacciones
        $totalVentas = DB::table('Venta')
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->count();

        // 4. Verificar si hay resultados
        if (!$reporte) {
            return response()->json([
                'periodo' => [
                    'desde' => $fechaInicio,
                    'hasta' => $fechaFin
                ],
                'resumen' => [
                    'total_operaciones' => 0,
                    'ingresos_brutos'   => 0.0,
                    'costo_inventario'  => 0.0,
                    'ganancia_real'     => 0.0,
                    'margen_promedio'   => '0%'
                ]
            ]);
        }

        return response()->json([
            'periodo' => [
                'desde' => $fechaInicio,
                'hasta' => $fechaFin
            ],
            'resumen' => [
                'total_operaciones' => $totalVentas,
                'ingresos_brutos'   => (float) $reporte->ingresos_totales,
                'costo_inventario'  => (float) $reporte->costo_total_mercancia,
                'ganancia_real'     => (float) $reporte->utilidad_neta,
                'margen_promedio'   => $reporte->ingresos_totales > 0 
                                        ? round(($reporte->utilidad_neta / $reporte->ingresos_totales) * 100, 2) . '%' 
                                        : '0%'
            ]
        ]);
    }
}