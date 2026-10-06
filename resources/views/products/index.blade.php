<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Productos</h1>
                <p class="text-sm text-slate-500">Catálogo y existencias del inventario</p>
            </div>

            @can('create', App\Models\Product::class)
                <a href="{{ route('products.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                    <x-icon name="plus" class="h-4 w-4" />
                    Nuevo producto
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        {{-- Filtros --}}
        <form method="GET" action="{{ route('products.index') }}"
              class="grid grid-cols-1 gap-3 border-b border-slate-200 p-4 md:grid-cols-2 xl:grid-cols-12">
            <div class="relative xl:col-span-4">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por SKU o nombre..."
                       class="w-full rounded-lg border-slate-300 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <select name="category"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 xl:col-span-2">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="stock"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 xl:col-span-2">
                <option value="">Todo el stock</option>
                <option value="normal" @selected($stock === 'normal')>Stock normal</option>
                <option value="low" @selected($stock === 'low')>Stock bajo</option>
                <option value="out" @selected($stock === 'out')>Agotados</option>
            </select>

            <select name="status"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 xl:col-span-2">
                <option value="">Todos los estados</option>
                <option value="active" @selected($status === 'active')>Activos</option>
                <option value="inactive" @selected($status === 'inactive')>Inactivos</option>
            </select>

            <div class="flex gap-2 xl:col-span-2">
                <button type="submit"
                        class="flex-1 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                    Filtrar
                </button>
                @if ($search !== '' || $categoryId || $status || $stock)
                    <a href="{{ route('products.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Producto</th>
                        <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 lg:table-cell">Categoría</th>
                        <th class="hidden px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 xl:table-cell">P. compra</th>
                        <th class="hidden px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 md:table-cell">P. venta</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Stock</th>
                        <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 sm:table-cell">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-mono text-slate-500">{{ $product->sku }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-slate-800">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400 lg:hidden">{{ $product->category->name }}</p>
                            </td>
                            <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-slate-600 lg:table-cell">{{ $product->category->name }}</td>
                            <td class="hidden whitespace-nowrap px-4 py-3 text-right text-sm text-slate-600 xl:table-cell">${{ number_format($product->purchase_price, 2) }}</td>
                            <td class="hidden whitespace-nowrap px-4 py-3 text-right text-sm text-slate-800 md:table-cell">${{ number_format($product->sale_price, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-800">{{ $product->stock }}</span>
                                    <x-stock-badge :product="$product" />
                                </div>
                            </td>
                            <td class="hidden whitespace-nowrap px-4 py-3 sm:table-cell">
                                <x-badge :color="$product->is_active ? 'green' : 'slate'">
                                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('products.show', $product) }}" title="Ver detalle"
                                       class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600">
                                        <x-icon name="eye" class="h-5 w-5" />
                                    </a>
                                    @can('update', $product)
                                        <a href="{{ route('products.edit', $product) }}" title="Editar"
                                           class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-amber-600">
                                            <x-icon name="pencil" class="h-5 w-5" />
                                        </a>
                                    @endcan
                                    @can('delete', $product)
                                        <button type="button" title="Eliminar"
                                                x-data x-on:click.prevent="$dispatch('open-modal', 'delete-product-{{ $product->id }}')"
                                                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-red-600">
                                            <x-icon name="trash" class="h-5 w-5" />
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-icon name="cube" class="h-6 w-6" />
                                </span>
                                <p class="mt-3 text-sm font-medium text-slate-700">No se encontraron productos</p>
                                <p class="text-sm text-slate-500">Prueba con otros filtros o registra un nuevo producto.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- Modales de confirmación (fuera de la tabla) --}}
    @foreach ($products as $product)
        @can('delete', $product)
            <x-confirm-delete
                :name="'delete-product-' . $product->id"
                :action="route('products.destroy', $product)"
                title="Eliminar producto"
                :message="'¿Seguro que deseas eliminar «' . $product->name . '»? Dejará de aparecer en el inventario, pero se conservará su historial de movimientos.'" />
        @endcan
    @endforeach
</x-app-layout>