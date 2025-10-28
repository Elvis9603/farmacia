<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas con Receta</title>
    <style>
        @page {
            margin: 100px 40px 60px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        body::before {
            content: "";
            position: fixed;
            top: 50%;
            left: 50%;
            width: 1000px;
            height: 1000px;
            background: url("data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/lara.png'))) }}") no-repeat center center;
            background-size: contain;
            opacity: 0.5; /* Ajusta la transparencia */
            transform: translate(-50%, -50%);
            z-index: -1; /* Para que quede detrás del texto */
            pointer-events: none;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            line-height: 1.2;
        }

        header img {
            position: absolute;
            left: 40px;
            top: 5px;
            width: 70px;
            height: 70px;
        }

        header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
        }

        header h2 {
            font-size: 14px;
            margin: 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #555;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .center {
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #555;
        }

        .firma {
            margin-top: 50px;
            text-align: center;
        }

        .firma div {
            display: inline-block;
            margin: 0 60px;
        }

        .firma hr {
            margin: 5px 0;
            border: none;
            border-top: 1px solid #000;
            width: 200px;
        }

    </style>
</head>
<body>

<header>
    <h1>Farmacia LimberthPool</h1>
    <h2>Reporte de Ventas Controladas con Receta</h2>
    <small>{{ now()->format('d/m/Y H:i') }}</small>
</header>

<main>
    <p><strong>Rango de fechas:</strong>
        {{ request()->fecha_inicio ? \Carbon\Carbon::parse(request()->fecha_inicio)->format('d/m/Y') : now()->format('d/m/Y') }}
        -
        {{ request()->fecha_fin ? \Carbon\Carbon::parse(request()->fecha_fin)->format('d/m/Y') : now()->format('d/m/Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Doctor</th>
                <th>Diagnóstico</th>
                <th>Medicamentos Vendidos</th>
                <th>Total (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
                @if($venta->receta) {{-- Solo mostrar las que tienen receta --}}
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $venta->cliente->nombre }}</td>
                        <td>{{ $venta->receta->nombreDoctor ?? 'N/A' }}</td>
                        <td>{{ $venta->receta->diagnostico ?? 'N/A' }}</td>
                        <td>
                            <ul style="margin: 0; padding-left: 15px;">
                                @foreach($venta->detalleVentas as $detalle)
                                    <li>{{ $detalle->producto->nombre }} ({{ $detalle->cantidad }} unds)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>Bs. {{ number_format($venta->total, 2) }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="6" class="center">No hay ventas con receta en el rango seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="firma">
        <div>
            <hr>
            <p>Firma del Encargado</p>
        </div>
        <div>
            <hr>
            <p>Firma FELCC</p>
        </div>
    </div>
</main>

<footer>
    <p>Farmacia LimberthPool - Reporte de Ventas Controladas con Receta © {{ date('Y') }}</p>
</footer>

</body>
</html>
