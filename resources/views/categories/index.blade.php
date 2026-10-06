<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Categorías</h1>
                <p class="text-sm text-slate-500">Organiza tus productos por categoría</p>
            </div>

            @can('create', App\Models\Category::class)
                <a href="{{ route('categories.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                    <x-icon name="plus" class="h-4 w-4" />
                    Nueva categoría
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        {{-- Filtros --}}
        <form method="GET" action="{{ route('categories.index') }}"
              class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre o descripción..."
                       class="w-full rounded-lg border-slate-300 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <select name="status"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todos los estados</option>
                <option value="active" @selected($status === 'active')>Activas</option>
                <option value="inactive" @selected($status === 'inactive')>Inactivas</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                    Filtrar
                </button>
                @if ($search !== '' || $status)
                    <a href="{{ route('categories.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</th>
                        <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 md:table-cell">Descripción</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">#{{ $category->id }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-slate-800">{{ $category->name }}</td>
                            <td class="hidden max-w-xs truncate px-4 py-3 text-sm text-slate-500 md:table-cell">
                                {{ $category->description ?: '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <x-badge :color="$category->is_active ? 'green' : 'red'">
                                    {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('categories.show', $category) }}" title="Ver detalle"
                                       class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600">
                                        <x-icon name="eye" class="h-5 w-5" />
                                    </a>
                                    @can('update', $category)
                                        <a href="{{ route('categories.edit', $category) }}" title="Editar"
                                           class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-amber-600">
                                            <x-icon name="pencil" class="h-5 w-5" />
                                        </a>
                                    @endcan
                                    @can('delete', $category)
                                        <button type="button" title="Eliminar"
                                                x-data x-on:click.prevent="$dispatch('open-modal', 'delete-category-{{ $category->id }}')"
                                                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-red-600">
                                            <x-icon name="trash" class="h-5 w-5" />
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-icon name="tag" class="h-6 w-6" />
                                </span>
                                <p class="mt-3 text-sm font-medium text-slate-700">No se encontraron categorías</p>
                                <p class="text-sm text-slate-500">Prueba con otros filtros o crea una nueva categoría.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Modales de confirmación (fuera de la tabla) --}}
    @foreach ($categories as $category)
        @can('delete', $category)
            <x-confirm-delete
                :name="'delete-category-' . $category->id"
                :action="route('categories.destroy', $category)"
                title="Eliminar categoría"
                :message="'¿Seguro que deseas eliminar la categoría «' . $category->name . '»? Esta acción no se puede deshacer.'" />
        @endcan
    @endforeach
</x-app-layout>