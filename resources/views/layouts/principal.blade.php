<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/flowbite@2.3.0/dist/flowbite.min.js"></script>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- Barra superior -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-4 py-3 flex justify-between items-center">
            <!-- Logo + Nombre -->
            <div class="flex items-center space-x-3">
                <img src="/img/logo.png" alt="Logo" class="w-10 h-10 rounded-lg">
                <span class="text-xl font-bold text-green-700">LimberthPool</span>
            </div>

            <!-- Usuario -->
            @auth('web')
            <div class="relative">
                <button id="user-menu-button" data-dropdown-toggle="dropdown-user"
                    class="flex items-center text-sm font-semibold text-gray-800 dark:text-gray-200 hover:text-green-600">
                    <i class="fa-solid fa-user mr-2"></i>{{ Auth::guard('web')->user()->nombre }}
                    <i class="fa-solid fa-chevron-down ml-2 text-xs"></i>
                </button>

                <div id="dropdown-user"
                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border dark:bg-gray-700 dark:border-gray-600">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                        {{-- <li><a href="{{ route('home') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a></li>
                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Configuración</a></li>
                        <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Earnings</a></li> --}}
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Sidebar lateral -->
    <aside class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="h-full px-3 py-4 overflow-y-auto">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="{{ route('home') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fa-solid fa-cart-shopping w-5 text-gray-500"></i>
                        <span class="ml-3">Ventas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('productos.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fa-solid fa-pills w-5 text-gray-500"></i>
                        <span class="ml-3">Productos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('categorias.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fa-solid fa-tags w-5 text-gray-500"></i>
                        <span class="ml-3">Categorías</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('usuarios.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fa-solid fa-users w-5 text-gray-500"></i>
                        <span class="ml-3">Usuarios</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('clientes.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-green-100 dark:text-white dark:hover:bg-gray-700">
                        <i class="fa-solid fa-user-tag w-5 text-gray-500"></i>
                        <span class="ml-3">Clientes</span>
                    </a>
                </li>

                <!-- Reportes con submenú -->
                <li>
                    <button type="button"
                        class="flex items-center w-full p-2 text-gray-900 rounded-lg hover:bg-green-100 dark:text-white dark:hover:bg-gray-700"
                        aria-controls="dropdown-reportes" data-collapse-toggle="dropdown-reportes">
                        <i class="fa-solid fa-chart-line w-5 text-gray-500"></i>
                        <span class="ml-3 flex-1 text-left">Reportes</span>
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <ul id="dropdown-reportes" class="hidden py-2 space-y-2">
                        <li>
                            <a href="{{ route('reportes.ventas') }}" class="flex items-center w-full p-2 pl-11 text-gray-700 rounded-lg hover:bg-green-50 dark:text-gray-200 dark:hover:bg-gray-700">
                                Ventas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reportes.inventario') }}" class="flex items-center w-full p-2 pl-11 text-gray-700 rounded-lg hover:bg-green-50 dark:text-gray-200 dark:hover:bg-gray-700">
                                Inventario
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main class="p-2 sm:ml-64 mt-16">
        <div class="p-4 bg-white rounded-lg shadow-md">
            @yield('content')
        </div>
    </main>

    @livewireScripts
</body>
</html>
