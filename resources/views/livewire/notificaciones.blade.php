<div>
    <div class="relative">
        <!-- Botón de notificaciones -->
        <button type="button" class="relative p-1 text-gray-600 hover:text-gray-900 focus:outline-none" x-data="{}" @click="$dispatch('open-dropdown', 'notificaciones-dropdown')">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            @php
                $noLeidas = count(array_filter($notificaciones->toArray(), function($n) { return !$n['leida']; }));
            @endphp
            
            @if($noLeidas > 0)
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                    {{ $noLeidas }}
                </span>
            @endif
        </button>

        <!-- Dropdown de notificaciones -->
        <div 
            x-data="{ open: false }" 
            x-show="open" 
            @open-dropdown.window="if ($event.detail === 'notificaciones-dropdown') open = true" 
            @click.away="open = false"
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50"
            style="display: none;"
        >
            <div class="py-2">
                <div class="px-4 py-2 bg-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-700">Notificaciones</h3>
                    <div class="flex space-x-2">
                        <button wire:click="toggleMostrarTodas" class="text-xs text-blue-600 hover:text-blue-800">
                            {{ $mostrarTodas ? 'Mostrar no leídas' : 'Mostrar todas' }}
                        </button>
                        <button wire:click="marcarTodasLeidas" class="text-xs text-blue-600 hover:text-blue-800">
                            Marcar todas como leídas
                        </button>
                    </div>
                </div>
                
                <div class="max-h-64 overflow-y-auto">
                    @forelse($notificaciones as $notificacion)
                        <div class="px-4 py-3 border-b border-gray-100 {{ $notificacion->leida ? 'bg-white' : 'bg-blue-50' }}">
                            <div class="flex justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $notificacion->titulo }}</p>
                                <button wire:click="marcarLeida({{ $notificacion->id_notificacion }})" class="text-xs text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">{{ $notificacion->mensaje }}</p>
                            <div class="mt-2 flex justify-between items-center">
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($notificacion->fecha_creacion)->diffForHumans() }}</span>
                                @if($notificacion->enlace)
                                    <a href="{{ $notificacion->enlace }}" class="text-xs text-blue-600 hover:text-blue-800">Ver detalles</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                            No hay notificaciones
                        </div>
                    @endforelse
                </div>
                
                <div class="px-4 py-2 bg-gray-100 text-center">
                    <a href="/notificaciones" class="text-xs text-blue-600 hover:text-blue-800">Ver todas las notificaciones</a>
                </div>
            </div>
        </div>
    </div>
</div>