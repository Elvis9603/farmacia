<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md z-50">
    <div class="max-w-md w-full mx-4 p-6 bg-white rounded-2xl shadow-xl">
        <h2 class="text-center text-2xl font-semibold text-gray-800 mb-6">Detalles del Proveedor</h2>

        <form wire:submit.prevent="enviarClick" class="space-y-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Proveedor</label>
                <input id="nombre" wire:model="nombre" type="text"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select id="tipo" wire:model="tipo" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                    <option value="">Seleccione un tipo</option>
                    <option value="distribuidora">Distribuidora</option>
                    <option value="individual">Individual</option>
                </select>
                @error('tipo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input id="telefono" wire:model="telefono" type="text"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="diasCambioAntesVencimiento" class="block text-sm font-medium text-gray-700">Días antes del vencimiento para cambio</label>
                <input id="diasCambioAntesVencimiento" wire:model="diasCambioAntesVencimiento" type="number"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                @error('diasCambioAntesVencimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-between space-x-4 mt-4">
                <button type="submit"
                    class="w-full bg-red-600 text-white font-bold py-3 rounded-md transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    Enviar
                </button>
                <button type="button" wire:click="closeModal"
                    class="w-full bg-gray-300 text-gray-800 font-bold py-3 rounded-md transition hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cerrar
                </button>
            </div>
        </form>
    </div>
</div>
