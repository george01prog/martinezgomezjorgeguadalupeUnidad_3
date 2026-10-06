@php
    $items = [
        ['label' => 'Dashboard',     'icon' => 'home',   'route' => 'dashboard',        'active' => 'dashboard'],
        ['label' => 'Productos',     'icon' => 'cube',   'route' => 'products.index',   'active' => 'products.*'],
        ['label' => 'Categorías',    'icon' => 'tag',    'route' => 'categories.index', 'active' => 'categories.*'],
        ['label' => 'Movimientos',   'icon' => 'arrows', 'route' => 'movements.index',  'active' => 'movements.*'],
        ['label' => 'Usuarios',      'icon' => 'users',  'route' => 'users.index',      'active' => 'users.*', 'admin' => true],
        ['label' => 'Reportes',      'icon' => 'chart',  'route' => 'reports.index',    'active' => 'reports.*'],
        ['label' => 'Configuración', 'icon' => 'cog',    'route' => 'profile.edit',     'active' => 'profile.*'],
    ];
@endphp

<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
     class="fixed inset-0 z-30 bg-slate-900/60 lg:hidden" style="display: none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col bg-slate-900 transition-transform duration-200 lg:translate-x-0">

    <div class="flex h-16 items-center justify-between border-b border-slate-800 px-5">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
                <x-icon name="cube" class="h-5 w-5" />
            </span>
            <span class="text-lg font-semibold text-white">Inventario</span>
        </a>
        <button type="button" @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden" aria-label="Cerrar menú">
            <x-icon name="x" class="h-6 w-6" />
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($items as $item)
            @continue(($item['admin'] ?? false) && ! auth()->user()->isAdmin())

            @php $isActive = request()->routeIs($item['active']); @endphp

            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                      {{ $isActive ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-icon :name="$item['icon']" class="h-5 w-5 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-slate-800 px-5 py-4 text-xs text-slate-500">
        Sesión: {{ auth()->user()->role_label }}
    </div>
</aside>