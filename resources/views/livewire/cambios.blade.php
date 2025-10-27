<div class="container mx-auto px-0">
    <h2 class="text-center text-3xl font-bold mb-6">CAMBIOS / DEVOLUCIONES</h2>

    <div class="flex justify-between items-center mb-4">
        <button wire:click="openModal" class="bg-red-600 text-white px-5 py-2 rounded">Registrar Cambio</button>
    </div>

    @if(session()->has('message'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('message') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Fecha</th>
                    <th class="px-4 py-2">Proveedor</th>
                    <th class="px-4 py-2">Motivo</th>
                    <th class="px-4 py-2">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devoluciones as $d)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $d->id_devolucion }}</td>
                        <td class="px-4 py-2">{{ $d->fecha }}</td>
                        <td class="px-4 py-2">{{ \App\Models\ProveedorModel::find($d->id_proveedor)->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $d->motivo }}</td>
                        <td class="px-4 py-2">{{ $d->estado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-11/12 md:w-3/4 p-6">
                <h3 class="text-xl mb-4 font-bold">Registrar Devolución</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label>Fecha</label>
                        <input type="date" wire:model="fecha" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label>Proveedor</label>
                        <select wire:model.live="proveedor" class="w-full border rounded px-3 py-2">
                            <option value="">-- Seleccione --</option>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="text-sm mt-1 text-gray-600">Seleccionado: {{ $proveedor }}</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Motivo</label>
                    <input type="text" wire:model="motivo" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-3">
                    <h4 class="font-semibold mb-2">Agregar Productos</h4>

                    @if($proveedor == '')
                        <div class="bg-blue-100 text-blue-800 p-2 rounded">Seleccione un proveedor para ver los productos disponibles.</div>
                    @elseif($productos->count() == 0)
                        <div class="bg-yellow-100 text-yellow-800 p-2 rounded">No hay productos registrados para este proveedor.</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                            @foreach($productos as $prod)
                                <div class="border rounded p-2 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold">{{ $prod->nombre }}</div>
                                        <div class="text-sm">Stock: {{ $prod->stock }}</div>
                                    </div>
                                    <div>
                                        <button wire:click.prevent="agregarProducto({{ $prod->id_producto }})"
                                            class="bg-green-500 text-white px-3 py-1 rounded">
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <h4 class="font-semibold mb-2">Detalle</h4>
                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cant</th>
                                <th>PU</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $it)
                                <tr>
                                    <td>{{ $it['nombre'] }}</td>
                                    <td>{{ $it['cantidad'] }}</td>
                                    <td>Bs {{ number_format($it['precio_unitario'], 2) }}</td>
                                    <td>Bs {{ number_format($it['subtotal'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <button wire:click="guardarCambio" class="bg-red-600 text-white px-4 py-2 rounded">Guardar</button>
                    <button wire:click="$set('showModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </div>
        </div>
    @endif
</div>