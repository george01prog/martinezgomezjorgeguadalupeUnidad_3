<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $product->name }}</h1>
                <p class="text-sm text-slate-500">SKU <span class="font-mono">{{ $product->sku }}</span></p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    Volver
                </a>
                @can('update', $product)
                    <a href="{{ route('products.edit', $product) }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                        <x-icon name="pencil" class="h-4 w-4" />
                        Editar
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    {{-- Tarjetas resumen --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">Stock actual</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $product->stock }}</p>
            <div class="mt-2"><x-stock-badge :product="$product" /></div>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">Stock mínimo</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $product->min_stock }}</p>
            <p class="mt-2 text-xs text-slate-500">Umbral de alerta</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">Valor en inventario</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">${{ number_format($product->stock * $product->purchase_price, 2) }}</p>
            <p class="mt-2 text-xs text-slate-500">Stock × precio de compra</p>
        </div>
    </div>

    <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <dl class="divide-y divide-slate-100">
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">ID</dt>
                <dd class="col-span-2 text-sm text-slate-800">#{{ $product->id }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Categoría</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $product->category->name }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Descripción</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $product->description ?: 'Sin descripción' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Precio de compra</dt>
                <dd class="col-span-2 text-sm text-slate-800">${{ number_format($product->purchase_price, 2) }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Precio de venta</dt>
                <dd class="col-span-2 text-sm text-slate-800">${{ number_format($product->sale_price, 2) }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Estado</dt>
                <dd class="col-span-2">
                    <x-badge :color="$product->is_active ? 'green' : 'slate'">
                        {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                    </x-badge>
                </dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Fecha de registro</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Última actualización</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $product->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>
</x-app-layout>