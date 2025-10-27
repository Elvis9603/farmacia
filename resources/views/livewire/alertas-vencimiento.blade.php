<div class="container mx-auto px-4 py-6">
    
    
    <!-- Filtros -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <h3 class="text-lg font-semibold mb-3">Filtrar por:</h3>
        <div class="flex flex-wrap gap-4">
            <button wire:click="toggleVencidos" 
                    class="px-4 py-2 rounded-md {{ $mostrarVencidos ? 'bg-red-500 text-white' : 'bg-gray-200' }}">
                Vencidos
                @if(count($vencidos) > 0)
                    <span class="ml-2 bg-white text-red-500 text-xs font-bold px-2 py-1 rounded-full">
                        {{ count($vencidos) }}
                    </span>
                @endif
            </button>
            
            <button wire:click="toggleProximos" 
                    class="px-4 py-2 rounded-md {{ $mostrarProximos ? 'bg-yellow-500 text-white' : 'bg-gray-200' }}">
                Próximos a vencer (según proveedor)
                @if(count($proximosVencer) > 0)
                    <span class="ml-2 bg-white text-yellow-600 text-xs font-bold px-2 py-1 rounded-full">
                        {{ count($proximosVencer) }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    @php
        use Carbon\Carbon;
    @endphp

    <!-- Lista de productos vencidos -->
    @if((!$mostrarVencidos && !$mostrarProximos) || $mostrarVencidos)
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
            <div class="bg-red-100 px-4 py-2 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-700">Productos Vencidos</h3>
            </div>
            @if(count($vencidos) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días de atraso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vencidos as $producto)
                                @php
                                    $fechaVencimiento = $producto->fechaVencimiento ? Carbon::parse($producto->fechaVencimiento) : null;
                                @endphp
                                <tr class="hover:bg-red-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $fechaVencimiento ? $fechaVencimiento->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                        @if($fechaVencimiento)
                                            {{ (int)now()->diffInDays($fechaVencimiento, false) * -1 }} días
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $producto->stock }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        
                                        <button class="text-red-600 hover:text-red-900">Dar de baja</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 text-center text-gray-500">No hay productos vencidos</div>
            @endif
        </div>
    @endif

    <!-- Lista de productos próximos a vencer (según proveedor) -->
    @if((!$mostrarVencidos && !$mostrarProximos) || $mostrarProximos)
        @foreach($productosAgrupados as $dias => $productos)
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                <div class="bg-yellow-100 px-4 py-2 border-b border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-700">Productos Próximos a Vencer ({{ $dias }} días)</h3>
                </div>
                @if(count($productos) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días restantes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                    
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($productos as $producto)
                                    @php
                                        $fechaVencimiento = $producto->fechaVencimiento ? Carbon::parse($producto->fechaVencimiento) : null;
                                    @endphp
                                    <tr class="hover:bg-yellow-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $fechaVencimiento ? $fechaVencimiento->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-yellow-600">
                                            @if($fechaVencimiento)
                                                {{ (int)now()->diffInDays($fechaVencimiento, false) }} días
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $producto->stock }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $producto->proveedor->nombre ?? 'N/A' }}
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-gray-500">No hay productos próximos a vencer en {{ $dias }} días</div>
                @endif
            </div>
        @endforeach
        
        @if(count($productosAgrupados) == 0)
            <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                <div class="bg-yellow-100 px-4 py-2 border-b border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-700">Productos Próximos a Vencer</h3>
                </div>
                <div class="p-4 text-center text-gray-500">No hay productos próximos a vencer</div>
            </div>
        @endif
    @endif
</div>
