<header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
    <div class="flex items-center gap-3">
        <button type="button" @click="sidebarOpen = true"
                class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden" aria-label="Abrir menú">
            <x-icon name="menu" class="h-6 w-6" />
        </button>
        <span class="text-base font-semibold text-slate-800">{{ config('app.name') }}</span>
    </div>

    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button type="button" class="flex items-center gap-3 rounded-lg p-1.5 transition hover:bg-slate-100 focus:outline-none">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </span>
                <span class="hidden text-left sm:block">
                    <span class="block text-sm font-medium leading-tight text-slate-800">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-slate-500">{{ auth()->user()->role_label }}</span>
                </span>
                <x-icon name="chevron-down" class="h-4 w-4 text-slate-400" />
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="border-b border-slate-100 px-4 py-2">
                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                <x-badge color="indigo" class="mt-1">{{ auth()->user()->role_label }}</x-badge>
            </div>

            <x-dropdown-link :href="route('profile.edit')">Mi perfil</x-dropdown-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                                 onclick="event.preventDefault(); this.closest('form').submit();">
                    Cerrar sesión
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</header>