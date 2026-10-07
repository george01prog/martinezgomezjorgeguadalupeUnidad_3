<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500">Resumen general del inventario</p>
    </x-slot>

    {{-- Tarjetas estadísticas --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card title="Total de productos" :value="$stats['products']" icon="cube" color="indigo"
                     hint="Productos registrados" :href="route('products.index')" />

        <x-stat-card title="Total de categorías" :value="$stats['categories']" icon="tag" color="violet"
                     hint="Categorías registradas" :href="route('categories.index')" />

        <x-stat-card title="Productos disponibles" :value="$stats['available']" icon="check-circle" color="green"
                     hint="Con stock mayor a cero" :href="route('products.index')" />

        <x-stat-card title="Stock bajo" :value="$stats['low']" icon="exclamation" color="amber"
                     hint="En o por debajo del mínimo" :href="route('products.index', ['stock' => 'low'])" />

        <x-stat-card title="Agotados" :value="$stats['out']" icon="x-circle" color="red"
                     hint="Sin existencias" :href="route('products.index', ['stock' => 'out'])" />

        <x-stat-card title="Movimientos recientes" :value="$stats['recent']" icon="arrows" color="blue"
                     hint="Últimos 7 días"
                     :href="route('movements.index', ['from' => now()->subDays(6)->toDateString()])" />
    </div>

    {{-- Gráficos --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
            <h2 class="text-base font-semibold text-slate-800">Entradas y salidas</h2>
            <p class="text-sm text-slate-500">Unidades movidas en los últimos 7 días</p>
            <div class="relative mt-4 h-64">
                <canvas id="movementsChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-base font-semibold text-slate-800">Estado del stock</h2>
            <p class="text-sm text-slate-500">Distribución de productos</p>

            @if (array_sum($chart['stock']) > 0)
                <div class="relative mt-4 h-64">
                    <canvas id="stockChart"></canvas>
                </div>
            @else
                <p class="mt-10 text-center text-sm text-slate-400">Aún no hay productos registrados.</p>
            @endif
        </div>
    </div>

    {{-- Tablas --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Productos con stock bajo --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Productos con stock bajo</h2>
                    <p class="text-sm text-slate-500">Requieren reabastecimiento</p>
                </div>
                <a href="{{ route('products.index', ['stock' => 'low']) }}"
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Ver todos</a>
            </div>

            @if ($lowStockProducts->isEmpty())
                <div class="px-6 py-12 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <x-icon name="check-circle" class="h-6 w-6" />
                    </span>
                    <p class="mt-3 text-sm font-medium text-slate-700">¡Todo en orden!</p>
                    <p class="text-sm text-slate-500">Ningún producto está por debajo de su stock mínimo.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Producto</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Stock</th>
                                <th class="hidden px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 sm:table-cell">Mínimo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($lowStockProducts as $product)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('products.show', $product) }}"
                                           class="text-sm font-medium text-slate-800 hover:text-indigo-600">{{ $product->name }}</a>
                                        <p class="text-xs text-slate-400">
                                            <span class="font-mono">{{ $product->sku }}</span> · {{ $product->category->name }}
                                        </p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-slate-800">{{ $product->stock }}</td>
                                    <td class="hidden whitespace-nowrap px-4 py-3 text-right text-sm text-slate-500 sm:table-cell">{{ $product->min_stock }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-stock-badge :product="$product" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Últimos movimientos --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Últimos movimientos de inventario</h2>
                    <p class="text-sm text-slate-500">Actividad más reciente</p>
                </div>
                <a href="{{ route('movements.index') }}"
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Ver todos</a>
            </div>

            @if ($latestMovements->isEmpty())
                <div class="px-6 py-12 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <x-icon name="arrows" class="h-6 w-6" />
                    </span>
                    <p class="mt-3 text-sm font-medium text-slate-700">Sin movimientos todavía</p>
                    <p class="text-sm text-slate-500">Registra una entrada o salida para ver la actividad aquí.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Cant.</th>
                                <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 sm:table-cell">Usuario</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($latestMovements as $movement)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('movements.show', $movement) }}"
                                           class="text-sm font-medium text-slate-800 hover:text-indigo-600">{{ $movement->product->name }}</a>
                                        <p class="font-mono text-xs text-slate-400">{{ $movement->product->sku }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-movement-badge :movement="$movement" /></td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold
                                        {{ $movement->type === 'entrada' ? 'text-blue-600' : ($movement->type === 'salida' ? 'text-red-600' : 'text-violet-600') }}">
                                        {{ $movement->type === 'entrada' ? '+' : ($movement->type === 'salida' ? '−' : '=') }}{{ $movement->quantity }}
                                    </td>
                                    <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-slate-600 sm:table-cell">{{ $movement->user->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-slate-500">{{ $movement->created_at->format('d/m H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const data = @json($chart);

            Chart.defaults.font.family = 'Figtree, ui-sans-serif, system-ui, sans-serif';
            Chart.defaults.color = '#64748b';

            // Entradas y salidas de los últimos 7 días
            new Chart(document.getElementById('movementsChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        { label: 'Entradas', data: data.entradas, backgroundColor: '#3b82f6', borderRadius: 6 },
                        { label: 'Salidas', data: data.salidas, backgroundColor: '#ef4444', borderRadius: 6 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } },
                    },
                },
            });

            // Distribución del estado de stock
            const stockCanvas = document.getElementById('stockChart');

            if (stockCanvas) {
                new Chart(stockCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Stock normal', 'Stock bajo', 'Agotados'],
                        datasets: [{
                            data: data.stock,
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: { legend: { position: 'bottom' } },
                    },
                });
            }
        });
    </script>
</x-app-layout>