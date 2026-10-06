@props([
    'name',
    'action',
    'title' => 'Confirmar eliminación',
    'message' => 'Esta acción no se puede deshacer.',
])

<x-modal :name="$name" maxWidth="md">
    <form method="POST" action="{{ $action }}" class="p-6">
        @csrf
        @method('DELETE')

        <div class="flex items-start gap-4">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                <x-icon name="exclamation" class="h-6 w-6" />
            </span>
            <div>
                <h3 class="text-lg font-semibold text-slate-800">{{ $title }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" x-on:click="$dispatch('close')"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Cancelar
            </button>
            <button type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Sí, eliminar
            </button>
        </div>
    </form>
</x-modal>