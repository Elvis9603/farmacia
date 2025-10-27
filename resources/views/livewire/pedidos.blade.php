<div class="container mx-auto px-0">
    <h2 class="text-center text-3xl font-bold mb-6">PEDIDOS</h2>

    <div class="flex justify-between items-center mb-6">
        <button wire:click="openModal" class="bg-red-600 text-white px-5 py-2 rounded">Generar Pedido</button>
        <button wire:click="openModalProveedor" class="bg-blue-600 text-white px-5 py-2 rounded">Agregar Proveedor</button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('message') }}</div>
    @endif

    <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-6">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Fecha</th>
                    <th class="px-4 py-2">Proveedor</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $p)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $p->id_pedido }}</td>
                        <td class="px-4 py-2">{{ $p->fecha }}</td>
                        <td class="px-4 py-2">{{ $p->proveedor->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-2">Bs {{ number_format($p->total, 2) }}</td>
                        <td class="px-4 py-2">{{ ucfirst($p->estado) }}</td>
                        <td class="px-4 py-2">
                            @if($p->estado == 'pendiente')
                                <button wire:click="marcarRecibido({{ $p->id_pedido }})" class="bg-blue-500 text-white px-3 py-1 rounded">Marcar recibido</button>
                            @else
                                <span class="text-sm text-gray-600">Recibido</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal de Pedido -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-11/12 md:w-3/4 p-6">
                <h3 class="text-xl mb-4 font-bold">Nuevo Pedido</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-1">Fecha</label>
                        <input type="date" wire:model="fecha" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1">Proveedor</label>
                        <select wire:model.live="proveedor" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione un proveedor</option>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="mt-2 text-sm text-gray-600">
                            Proveedor seleccionado: <strong>{{ $proveedor }}</strong>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Agregar Productos</h4>

                    @if(empty($proveedor))
                        <div class="p-3 bg-blue-100 text-blue-800 rounded">Seleccione un proveedor para ver los productos disponibles</div>
                    @elseif($productos->isEmpty())
                        <div class="p-3 bg-yellow-100 text-yellow-800 rounded">No hay productos registrados para este proveedor</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 max-h-40 overflow-y-auto">
                            @foreach($productos as $prod)
                                <div class="border rounded p-2 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold">{{ $prod->nombre }}</div>
                                        <div class="text-sm">Compra: Bs {{ $prod->precioCompra }}</div>
                                    </div>
                                    <button wire:click="agregarProducto({{ $prod->id_producto }})"
                                            class="bg-green-500 text-white px-3 py-1 rounded">
                                        Agregar
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Detalle</h4>
                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left">Producto</th>
                                <th>Cant</th>
                                <th>PU</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $it)
                                <tr>
                                    <td>{{ $it['nombre'] }}</td>
                                    <td>
                                        <input type="number" min="1" value="{{ $it['cantidad'] }}"
                                               wire:change="actualizarCantidad({{ $index }}, $event.target.value)"
                                               class="w-20 border rounded px-2 py-1">
                                    </td>
                                    <td>Bs {{ number_format($it['precio_unitario'], 2) }}</td>
                                    <td>Bs {{ number_format($it['subtotal'], 2) }}</td>
                                    <td>
                                        <button wire:click.prevent="eliminarItem({{ $index }})"
                                                class="text-red-500">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-right mt-2 font-bold">Total: Bs {{ number_format($total, 2) }}</div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="guardarPedido" class="bg-red-600 text-white px-4 py-2 rounded">Guardar Pedido</button>
                    <button wire:click="$set('showModal', false)" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Proveedor -->
    @if($showModalProveedor)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-11/12 md:w-1/2 p-6">
                <h3 class="text-xl font-bold mb-4">Registrar Nuevo Proveedor</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">Nombre</label>
                        <input type="text" wire:model="nombreProveedor" class="w-full border rounded px-3 py-2">
                        @error('nombreProveedor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1">Teléfono</label>
                        <input type="text" wire:model="telefonoProveedor" class="w-full border rounded px-3 py-2">
                        @error('telefonoProveedor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block mb-1">Tipo</label>
                    <select wire:model="tipoProveedor" class="w-full border rounded px-3 py-2">
                        <option value="distribuidora">Distribuidora</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label class="block mb-1">Días para cambio antes del vencimiento</label>
                    <input type="number" wire:model="diasCambio" min="0" class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex justify-end mt-6 space-x-2">
                    <button wire:click="guardarProveedor" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                    <button wire:click="$set('showModalProveedor', false)" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </div>
        </div>
    @endif
</div>
