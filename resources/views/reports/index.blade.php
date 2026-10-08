<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Reportes</h1>
        <p class="text-sm text-slate-500">Consulta y exporta información del inventario</p>
    </x-slot>

    @php
        $exportParams = array_filter(array_merge(request()->except('page'), ['report' => $report]));
    @endphp

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-semibold">Corrige los siguientes errores:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Selector de reporte --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach ($reportList as $key => $label)
            <a href="{{ route('reports.index', array_filter(array_merge(request()->except(['page', 'report', 'type']), ['report' => $key]))) }}"
               class="rounded-full px-4 py-2 text-sm font-medium transition
                      {{ $report === $key ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Filtros --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <form method="GET" action="{{ route('reports.index') }}" class="p-4">
            <input type="hidden" name="report" value="{{ $report }}">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="product" class="mb-1 block text-xs font-medium text-slate-500">Producto</label>
                    <select id="product" name="product"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos los productos</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" @selected((string) $filters['product'] === (string) $p->id)>{{ $p->sku }} · {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category" class="mb-1 block text-xs font-medium text-slate-500">Categoría</label>
                    <select id="category" name="category"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas las categorías</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string) $filters['category'] === (string) $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($report === 'movements')
                    <div>
                        <label for="type" class="mb-1 block text-xs font-medium text-slate-500">Tipo de movimiento</label>
                        <select id="type" name="type"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los tipos</option>
                            @foreach (App\Models\InventoryMovement::TYPES as $key => $label)
                                <option value="{{ $key }}" @selected($filters['type'] === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label for="from" class="mb-1 block text-xs font-medium text-slate-500">Desde</label>
                    <input id="from" type="date" name="from" value="{{ $filters['from'] }}"
                           class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="to" class="mb-1 block text-xs font-medium text-slate-500">Hasta</label>
                    <input id="to" type="date" name="to" value="{{ $filters['to'] }}"
                           class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            @unless ($isMovement)
                <p class="mt-3 text-xs text-slate-400">En los reportes de productos, las fechas filtran por la fecha de registro del producto.</p>
            @endunless

            <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex gap-2">
                    <button type="submit"
                            class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                        Aplicar filtros
                    </button>
                    <a href="{{ route('reports.index', ['report' => $report]) }}"
                       class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('reports.export', array_merge($exportParams, ['format' => 'csv'])) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        <x-icon name="download" class="h-4 w-4" />
                        Exportar CSV
                    </a>
                    <a href="{{ route('reports.export', array_merge($exportParams, ['format' => 'pdf'])) }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                        <x-icon name="download" class="h-4 w-4" />
                        Exportar PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Resumen --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-{{ count($summary) }}">
        @foreach ($summary as [$label, $value, $icon, $color])
            <x-stat-card :title="$label" :value="$value" :icon="$icon" :color="$color" />
        @endforeach
    </div>

    {{-- Resultados --}}
    <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-800">{{ $title }}</h2>
            <p class="text-sm text-slate-500">{{ $results->total() }} registro(s) encontrado(s)</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    @if ($isMovement)
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Producto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Cantidad</th>
                            <th class="hidden px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 md:table-cell">Stock</th>
                            <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 lg:table-cell">Usuario</th>
                            <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 xl:table-cell">Motivo</th>
                        </tr>
                    @else
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Producto</th>
                            <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 lg:table-cell">Categoría</th>
                            <th class="hidden px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 xl:table-cell">P. compra</th>
                            <th class="hidden px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 md:table-cell">P. venta</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Stock</th>
                            <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 sm:table-cell">Estado</th>
                        </tr>
                    @endif
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($results as $item)
                        @if ($isMovement)
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-slate-800">{{ $item->product->name }}</p>
                                    <p class="font-mono text-xs text-slate-400">{{ $item->product->sku }}</p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3"><x-movement-badge :movement="$item" /></td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold
                                    {{ $item->type === 'entrada' ? 'text-blue-600' : ($item->type === 'salida' ? 'text-red-600' : 'text-violet-600') }}">
                                    {{ $item->type === 'entrada' ? '+' : ($item->type === 'salida' ? '−' : '=') }}{{ $item->quantity }}
                                </td>
                                <td class="hidden whitespace-nowrap px-4 py-3 text-right text-sm text-slate-500 md:table-cell">
                                    {{ $item->stock_before }} → <span class="font-semibold text-slate-800">{{ $item->stock_after }}</span>
                                </td>
                                <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-slate-600 lg:table-cell">{{ $item->user->name }}</td>
                                <td class="hidden max-w-xs truncate px-4 py-3 text-sm text-slate-500 xl:table-cell">{{ $item->reason ?: '—' }}</td>
                            </tr>
                        @else
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 font-mono text-sm text-slate-500">{{ $item->sku }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-slate-800">{{ $item->name }}</p>
                                    <p class="text-xs text-slate-400 lg:hidden">{{ $item->category->name }}</p>
                                </td>
                                <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-slate-600 lg:table-cell">{{ $item->category->name }}</td>
                                <td class="hidden whitespace-nowrap px-4 py-3 text-right text-sm text-slate-600 xl:table-cell">${{ number_format($item->purchase_price, 2) }}</td>
                                <td class="hidden whitespace-nowrap px-4 py-3 text-right text-sm text-slate-800 md:table-cell">${{ number_format($item->sale_price, 2) }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-slate-800">{{ $item->stock }}</span>
                                        <x-stock-badge :product="$item" />
                                    </div>
                                </td>
                                <td class="hidden whitespace-nowrap px-4 py-3 sm:table-cell">
                                    <x-badge :color="$item->is_active ? 'green' : 'slate'">{{ $item->is_active ? 'Activo' : 'Inactivo' }}</x-badge>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-icon name="chart" class="h-6 w-6" />
                                </span>
                                <p class="mt-3 text-sm font-medium text-slate-700">No hay datos para este reporte</p>
                                <p class="text-sm text-slate-500">Prueba con otros filtros.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($results->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $results->links() }}
            </div>
        @endif
    </div>
</x-app-layout>