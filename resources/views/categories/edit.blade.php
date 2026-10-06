<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Editar categoría</h1>
        <p class="text-sm text-slate-500">Modifica los datos de «{{ $category->name }}»</p>
    </x-slot>

    <div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')

            @include('categories._form')

            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="{{ route('categories.index') }}"
                   class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Cancelar
                </a>
                <x-primary-button>Actualizar categoría</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>