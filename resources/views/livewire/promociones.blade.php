<div class="container mx-auto px-0">
    <h2 class="text-center text-3xl font-bold mb-6">PROMOCIONES</h2>

    <div class="flex justify-between items-center mb-4">
        <button wire:click="openModal" class="bg-red-600 text-white px-5 py-2 rounded">Nueva Promoción</button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('message') }}</div>
    @endif

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Producto</th>
                    <th class="px-4 py-2">Descuento (%)</th>
                    <th class="px-4 py-2">Fecha inicio</th>
                    <th class="px-4 py-2">Fecha fin</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($promociones as $prom)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $prom->id_promocion }}</td>
                        <td class="px-4 py-2">{{ $prom->producto->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $prom->porcentaje_descuento }}</td>
                        <td class="px-4 py-2">{{ $prom->fecha_inicio }}</td>
                        <td class="px-4 py-2">{{ $prom->fecha_fin }}</td>
                        <td class="px-4 py-2">
                            <button wire:click="eliminarPromocion({{ $prom->id_promocion }})" class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-11/12 md:w-1/2 p-6">
                <h3 class="text-xl mb-4 font-bold">Nueva Promoción</h3>

                <div class="mb-3">
                    <label>Producto</label>
                    <select wire:model="producto" class="w-full border rounded px-3 py-2">
                        <option value="">-- Seleccione --</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id_producto }}">{{ $prod->nombre }} (Bs {{ $prod->precioVenta }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label>% Descuento</label>
                        <input type="number" min="1" max="100" wire:model="porcentaje_descuento" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label>Fecha inicio</label>
                        <input type="date" wire:model="fecha_inicio" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label>Fecha fin</label>
                        <input type="date" wire:model="fecha_fin" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button wire:click="guardar" class="bg-red-600 text-white px-4 py-2 rounded">Guardar</button>
                    <button wire:click="$set('showModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </div>
        </div>
    @endif
</div>
