<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Movimiento #{{ $movement->id }}</h1>
                <p class="text-sm text-slate-500">Detalle del movimiento de inventario</p>
            </div>
            <a href="{{ route('movements.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <x-icon name="arrow-left" class="h-4 w-4" />
                Volver
            </a>
        </div>
    </x-slot>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">Stock antes</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $movement->stock_before }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">Cantidad</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $movement->quantity }}</p>
            <div class="mt-2"><x-movement-badge :movement="$movement" /></div>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">Stock después</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $movement->stock_after }}</p>
            <p class="mt-2 text-xs text-slate-500">
                Diferencia: {{ $movement->difference > 0 ? '+' : '' }}{{ $movement->difference }}
            </p>
        </div>
    </div>

    <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <dl class="divide-y divide-slate-100">
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Producto</dt>
                <dd class="col-span-2 text-sm text-slate-800">
                    @if ($movement->product->trashed())
                        {{ $movement->product->name }} <x-badge color="slate">Eliminado</x-badge>
                    @else
                        <a href="{{ route('products.show', $movement->product) }}" class="text-indigo-600 hover:underline">{{ $movement->product->name }}</a>
                    @endif
                    <span class="ml-1 font-mono text-xs text-slate-400">{{ $movement->product->sku }}</span>
                </dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Categoría</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $movement->product->category->name }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Registrado por</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $movement->user->name }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Fecha</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $movement->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Motivo</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $movement->reason ?: 'Sin observaciones' }}</dd>
            </div>
        </dl>
    </div>
</x-app-layout>