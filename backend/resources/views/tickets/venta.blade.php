<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta #{{ $venta->id_venta }}</title>
    <style>
        @page { margin: 0; size: 80mm 297mm; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            width: 300px;
            margin: 0 auto;
            padding: 10px 15px;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        th, td { padding: 2px 0; }
        .header { border-bottom: 2px dashed #000; padding-bottom: 8px; margin-bottom: 8px; }
        .footer { border-top: 2px dashed #000; padding-top: 8px; margin-top: 8px; }
        .item-row td:last-child { text-align: right; }
    </style>
</head>
<body>

    <div class="center header">
        <h2 style="margin:0; font-size:15px;">FERRE CONTROL</h2>
        <p style="margin:2px 0; font-size:11px;">RFC: XAXX010101000</p>
        <p style="margin:2px 0; font-size:11px;">Av. Principal #123, Col. Centro, Orizaba, Veracruz</p>
        <p style="margin:2px 0; font-size:11px;">Tel: (55) 1234-5678</p>
        <p style="margin:8px 0 4px; font-size:13px;">TICKET DE VENTA</p>
        <p style="margin:0; font-size:11px;">
            {{ $venta->created_at->format('d/m/Y H:i:s') }}
        </p>
        <p class="bold" style="margin:4px 0 0;">#{{ str_pad($venta->id_venta, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    @if($venta->cliente && $venta->cliente->id_cliente != 1)
    <div class="center" style="margin:8px 0; font-size:11px;">
        Cliente: {{ $venta->cliente->nombre }} {{ $venta->cliente->ap_paterno }}
    </div>
    @endif

    <table>
        <thead>
            <tr style="border-bottom:1px solid #000;">
                <th class="left" style="text-align:left; width:45%">Producto</th>
                <th class="center" style="width:15%">Cant.</th>
                <th class="right" style="width:20%">Precio</th>
                <th class="right" style="width:20%">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr class="item-row">
                <td style="text-align:left;">{{ $detalle->producto->nombre }}</td>
                <td class="center">{{ $detalle->cantidad }}</td>
                <td class="right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="right">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td class="bold">TOTAL:</td>
                <td class="right bold">${{ number_format($venta->total_venta, 2) }}</td>
            </tr>
            <tr>
                <td>Método de pago:</td>
                <td class="right">{{ $venta->metodo_pago }}</td>
            </tr>
            <tr>
                <td>Monto recibido:</td>
            <td class="right">${{ number_format($venta->pago_cliente ?? $venta->total_venta, 2) }}</td>
            </tr>
            <tr>
                <td>Cambio:</td>
                <td class="right">${{ number_format($venta->cambio, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="center" style="margin-top:15px; font-size:11px;">
        ¡Gracias por tu compra!<br>
        Vuelve pronto
    </div>

    <script>
        window.onload = () => {
            window.print();
            // Opcional: cerrar ventana automáticamente
            // setTimeout(() => window.close(), 800);
        };
    </script>
</body>
</html>