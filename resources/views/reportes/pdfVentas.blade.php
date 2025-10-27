<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; }

        body::before {
            content: "";
            position: fixed;
            top: 50%;
            left: 50%;
            width: 600px;
            height: 600px;
            background: url("data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/escudo.png'))) }}") no-repeat center center;
            background-size: contain;
            opacity: 0.1; /* Ajusta la transparencia */
            transform: translate(-50%, -50%);
            z-index: -1; /* Para que quede detrás del texto */
            pointer-events: none;
        }

        .container { width: 100%; padding: 2rem; }
        .header { text-align: center; margin-bottom: 2rem; }
        h1 { font-size: 2rem; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        .table th, .table td { padding: 0.8rem; text-align: left; border: 1px solid #ddd; }
        .table th { background-color: #f4f4f4; font-weight: bold; }
        .table tr:nth-child(even) { background-color: #f9f9f9; }
        .total { font-weight: bold; font-size: 1.2rem; margin-top: 1rem; }
        .footer { text-align: center; margin-top: 2rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Farmacia "LimberthPool"</h1>
            <p>Reporte de Ventas</p>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha de Venta</th>
                    <th>Cliente</th>
                    <th>Usuario</th>
                    <th>Medicamentos Vendidos</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $venta->cliente->nombre }} </td>
                        <td>{{ $venta->usuario->nombre }} </td>
                        <td>
                            <ul>
                                @foreach($venta->detalleVentas as $detalle)
                                    <li>{{ $detalle->producto->nombre }} - Cantidad:{{ $detalle->cantidad }} - Bs. {{ number_format($detalle->subtotal, 2) }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>Bs. {{ number_format($venta->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total">
            <p>Total General: Bs. {{ number_format($ventas->sum('total'), 2) }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Farmacia "LimberthPool" | Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
