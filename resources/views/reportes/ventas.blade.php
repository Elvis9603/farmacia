@extends('layouts.principal')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Reporte de Ventas</h2>
    <form method="GET" action="{{ route('reportes.ventas') }}" class="mb-4">
        <div class="flex items-center space-x-4 mb-4">
            <div class="flex items-center">
                <label for="fecha_inicio" class="mr-2">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request()->fecha_inicio }}" class="p-2 border rounded">
            </div>

            <div class="flex items-center">
                <label for="fecha_fin" class="mr-2">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="{{ request()->fecha_fin }}" class="p-2 border rounded">
            </div>

            <div class="flex items-center">
                <label for="usuario_id" class="mr-2">Usuario:</label>
                <select id="usuario_id" name="usuario_id" class="p-2 border rounded">
                    <option value="">Seleccione un usuario o mostrar todas las ventas</option>
                    <option value="">Mostrar todas las ventas</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ request()->usuario_id == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center">
                <button type="submit" class="p-2 bg-blue-600 text-white rounded">Filtrar</button>
            </div>
        </div>
    </form>
    <div class="mb-4">
        <a href="{{ route('reportes.ventas.pdf', request()->all()) }}" class="p-2 bg-blue-600 text-white rounded mr-2">Descargar PDF</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2 border">Fecha de Venta</th>
                    <th class="px-4 py-2 border">Cliente</th>
                    <th class="px-4 py-2 border">Usuario</th>
                    <th class="px-4 py-2 border">Medicamentos Vendidos</th>
                    <th class="px-4 py-2 border">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                    <tr class="border">
                        <td class="px-4 py-2 border">{{ $venta->fecha }}</td>
                        <td class="px-4 py-2 border">{{ $venta->cliente->nombre }} </td>
                        <td class="px-4 py-2 border">{{ $venta->usuario->nombre }} </td>
                        <td class="px-4 py-2 border">
                            <ul>
                                @foreach($venta->detalleVentas as $detalle)
                                    <li>{{ $detalle->producto->nombre }}  (Cantidad:{{ $detalle->cantidad }}) - Bs. {{ number_format($detalle->subtotal, 2) }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-4 py-2 border">Bs. {{ number_format($venta->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
