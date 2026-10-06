<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $category->name }}</h1>
                <p class="text-sm text-slate-500">Detalle de la categoría</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('categories.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    Volver
                </a>
                @can('update', $category)
                    <a href="{{ route('categories.edit', $category) }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                        <x-icon name="pencil" class="h-4 w-4" />
                        Editar
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <dl class="divide-y divide-slate-100">
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">ID</dt>
                <dd class="col-span-2 text-sm text-slate-800">#{{ $category->id }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Nombre</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $category->name }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Descripción</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $category->description ?: 'Sin descripción' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Estado</dt>
                <dd class="col-span-2">
                    <x-badge :color="$category->is_active ? 'green' : 'red'">
                        {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                    </x-badge>
                </dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Registrada</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $category->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4 py-3">
                <dt class="text-sm font-medium text-slate-500">Actualizada</dt>
                <dd class="col-span-2 text-sm text-slate-800">{{ $category->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>
</x-app-layout>