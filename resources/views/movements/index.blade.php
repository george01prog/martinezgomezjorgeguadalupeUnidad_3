<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Movimientos</h1>
                <p class="text-sm text-slate-500">Historial de entradas, salidas y ajustes de inventario</p>
            </div>

            <a href="{{ route('movements.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                <x-icon name="plus" class="h-4 w-4" />
                Registrar movimiento
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        {{-- Filtros --}}
        <form method="GET" action="{{ route('movements.index') }}"
              class="grid grid-cols-1 gap-3 border-b border-slate-200 p-4 md:grid-cols-2 xl:grid-cols-12">
            <select name="product"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 xl:col-span-3">
                <option value="">Todos los productos</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected((string) $productId === (string) $p->id)>{{ $p->sku }} · {{ $p->name }}</option>
                @endforeach
            </select>

            <select name="type"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 xl:col-span-2">
                <option value="">Todos los tipos</option>
                @foreach (App\Models\InventoryMovement::TYPES as $key => $label)
                    <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <div class="xl:col-span-2">
                <input type="date" name="from" value="{{ $from }}" title="Desde"
                       class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="xl:col-span-2">
                <input type="date" name="to" value="{{ $to }}" title="Hasta"
                       class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="flex gap-2 xl:col-span-3">
                <button type="submit"
                        class="flex-1 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                    Filtrar
                </button>
                @if ($type || $productId || $from || $to)
                    <a href="{{ route('movements.index') }}"
                       class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Cantidad</th>
                        <th class="hidden px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 md:table-cell">Stock</th>
                        <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 lg:table-cell">Usuario</th>
                        <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 xl:table-cell">Motivo</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($movements as $movement)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-slate-800">{{ $movement->product->name }}</p>
                                <p class="font-mono text-xs text-slate-400">{{ $movement->product->sku }}</p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3"><x-movement-badge :movement="$movement" /></td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold
                                {{ $movement->type === 'entrada' ? 'text-blue-600' : ($movement->type === 'salida' ? 'text-red-600' : 'text-violet-600') }}">
                                {{ $movement->type === 'entrada' ? '+' : ($movement->type === 'salida' ? '−' : '=') }}{{ $movement->quantity }}
                            </td>
                            <td class="hidden whitespace-nowrap px-4 py-3 text-right text-sm text-slate-500 md:table-cell">
                                {{ $movement->stock_before }} → <span class="font-semibold text-slate-800">{{ $movement->stock_after }}</span>
                            </td>
                            <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-slate-600 lg:table-cell">{{ $movement->user->name }}</td>
                            <td class="hidden max-w-xs truncate px-4 py-3 text-sm text-slate-500 xl:table-cell">{{ $movement->reason ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <a href="{{ route('movements.show', $movement) }}" title="Ver detalle"
                                   class="inline-flex rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600">
                                    <x-icon name="eye" class="h-5 w-5" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-icon name="arrows" class="h-6 w-6" />
                                </span>
                                <p class="mt-3 text-sm font-medium text-slate-700">No hay movimientos registrados</p>
                                <p class="text-sm text-slate-500">Prueba con otros filtros o registra un movimiento.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($movements->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</x-app-layout>