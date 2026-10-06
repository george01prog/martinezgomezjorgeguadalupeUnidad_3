<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500">Resumen general del inventario</p>
    </x-slot>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">¡Bienvenido, {{ auth()->user()->name }}!</h2>
        <p class="mt-2 text-sm text-slate-500">
            Tu rol: <x-badge color="indigo">{{ auth()->user()->role_label }}</x-badge>
        </p>
        <p class="mt-4 text-sm text-slate-500">Las tarjetas estadísticas se agregarán en la etapa del dashboard.</p>
    </div>
</x-app-layout>