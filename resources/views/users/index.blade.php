<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Usuarios</h1>
                <p class="text-sm text-slate-500">Administra las cuentas y los roles del sistema</p>
            </div>

            <a href="{{ route('users.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                <x-icon name="plus" class="h-4 w-4" />
                Nuevo usuario
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
        {{-- Filtros --}}
        <form method="GET" action="{{ route('users.index') }}"
              class="grid grid-cols-1 gap-3 border-b border-slate-200 p-4 md:grid-cols-2 xl:grid-cols-12">
            <div class="relative xl:col-span-5">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre o correo..."
                       class="w-full rounded-lg border-slate-300 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <select name="role"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 xl:col-span-2">
                <option value="">Todos los roles</option>
                <option value="admin" @selected($role === 'admin')>Administradores</option>
                <option value="user" @selected($role === 'user')>Usuarios</option>
            </select>

            <select name="status"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 xl:col-span-2">
                <option value="">Todos los estados</option>
                <option value="active" @selected($status === 'active')>Activos</option>
                <option value="inactive" @selected($status === 'inactive')>Inactivos</option>
            </select>

            <div class="flex gap-2 xl:col-span-3">
                <button type="submit"
                        class="flex-1 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                    Filtrar
                </button>
                @if ($search !== '' || $role || $status)
                    <a href="{{ route('users.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Rol</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                        <th class="hidden px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 md:table-cell">Registrado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        @php $isSelf = $u->id === auth()->id(); @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                        {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $u->name }}
                                            @if ($isSelf)
                                                <span class="ml-1 text-xs font-normal text-slate-400">(tú)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-500">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <x-badge :color="$u->isAdmin() ? 'indigo' : 'slate'">{{ $u->role_label }}</x-badge>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <x-badge :color="$u->is_active ? 'green' : 'red'">
                                    {{ $u->is_active ? 'Activo' : 'Inactivo' }}
                                </x-badge>
                            </td>
                            <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-slate-500 md:table-cell">
                                {{ $u->created_at->format('d/m/Y') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('users.edit', $u) }}" title="Editar"
                                       class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-amber-600">
                                        <x-icon name="pencil" class="h-5 w-5" />
                                    </a>
                                    @can('toggle', $u)
                                        <button type="button" title="{{ $u->is_active ? 'Desactivar' : 'Activar' }}"
                                                x-data x-on:click.prevent="$dispatch('open-modal', 'toggle-user-{{ $u->id }}')"
                                                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 {{ $u->is_active ? 'hover:text-red-600' : 'hover:text-emerald-600' }}">
                                            <x-icon name="power" class="h-5 w-5" />
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <x-icon name="users" class="h-6 w-6" />
                                </span>
                                <p class="mt-3 text-sm font-medium text-slate-700">No se encontraron usuarios</p>
                                <p class="text-sm text-slate-500">Prueba con otros filtros o registra un nuevo usuario.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Modales de activar/desactivar (fuera de la tabla) --}}
    @foreach ($users as $u)
        @can('toggle', $u)
            <x-modal :name="'toggle-user-' . $u->id" maxWidth="md">
                <form method="POST" action="{{ route('users.toggle', $u) }}" class="p-6">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-start gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $u->is_active ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }}">
                            <x-icon name="power" class="h-6 w-6" />
                        </span>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800">
                                {{ $u->is_active ? 'Desactivar usuario' : 'Activar usuario' }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                @if ($u->is_active)
                                    ¿Desactivar a «{{ $u->name }}»? No podrá iniciar sesión y, si está conectado, su sesión se cerrará en la siguiente acción.
                                @else
                                    ¿Activar a «{{ $u->name }}»? Podrá iniciar sesión nuevamente.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close')"
                                class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2
                                       {{ $u->is_active ? 'bg-red-600 hover:bg-red-500 focus:ring-red-500' : 'bg-emerald-600 hover:bg-emerald-500 focus:ring-emerald-500' }}">
                            {{ $u->is_active ? 'Sí, desactivar' : 'Sí, activar' }}
                        </button>
                    </div>
                </form>
            </x-modal>
        @endcan
    @endforeach
</x-app-layout>