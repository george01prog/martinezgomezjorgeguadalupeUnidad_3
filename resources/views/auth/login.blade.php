<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar sesión · {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-700 antialiased">
@php
    // [x, ancho, alto, color] por estante (la clave es la altura del estante)
    $shelves = [
        100 => [[40, 62, 52, '#818cf8'], [108, 50, 40, '#fbbf24'], [164, 70, 56, '#a78bfa'], [240, 48, 44, '#34d399'], [294, 60, 50, '#38bdf8'], [360, 28, 38, '#fb7185']],
        190 => [[38, 58, 48, '#fb7185'], [102, 70, 58, '#818cf8'], [178, 46, 40, '#34d399'], [230, 64, 54, '#fbbf24'], [300, 40, 36, '#38bdf8'], [346, 40, 50, '#a78bfa']],
        280 => [[40, 54, 44, '#38bdf8'], [100, 68, 56, '#34d399'], [174, 50, 42, '#fb7185'], [230, 72, 52, '#818cf8'], [308, 44, 38, '#fbbf24'], [358, 30, 50, '#a78bfa']],
    ];
@endphp

<div class="min-h-screen lg:grid lg:grid-cols-2">

    {{-- ============ PANEL IZQUIERDO: escaparate ============ --}}
    <aside class="relative hidden overflow-hidden bg-slate-900 lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-16">

        {{-- Foto opcional: guarda una imagen en public/images/login-bg.jpg y aparecerá sola --}}
        @if (file_exists(public_path('images/login-bg.jpg')))
            <img src="{{ asset('images/login-bg.jpg') }}" alt=""
                 class="anim-kenburns absolute inset-0 h-full w-full object-cover opacity-30">
        @endif

        {{-- Degradado y manchas animadas --}}
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/90 to-indigo-900/80"></div>
        <div class="anim-blob absolute -left-24 -top-24 h-96 w-96 rounded-full bg-indigo-600/40 blur-3xl"></div>
        <div class="anim-blob absolute -bottom-32 right-0 h-[28rem] w-[28rem] rounded-full bg-violet-600/30 blur-3xl" style="animation-delay: -6s"></div>
        <div class="anim-blob absolute left-1/3 top-1/2 h-72 w-72 rounded-full bg-sky-500/20 blur-3xl" style="animation-delay: -12s"></div>

        {{-- Cuadrícula decorativa --}}
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 44px 44px;"></div>

        {{-- Marca --}}
        <div class="anim-fade-up relative z-10 flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-500 text-white shadow-lg shadow-indigo-900/50">
                <x-icon name="cube" class="h-6 w-6" />
            </span>
            <span class="text-xl font-bold text-white">{{ config('app.name') }}</span>
        </div>

        {{-- Titular y escena --}}
        <div class="relative z-10">
            <h2 class="anim-fade-up text-4xl font-extrabold leading-tight text-white xl:text-5xl" style="animation-delay: 0.1s">
                Tu inventario,<br>
                <span class="bg-gradient-to-r from-indigo-300 to-sky-300 bg-clip-text text-transparent">siempre bajo control.</span>
            </h2>
            <p class="anim-fade-up mt-4 max-w-md text-base text-slate-300" style="animation-delay: 0.2s">
                Productos, existencias y movimientos en un solo lugar, como el almacén de una tienda bien organizada.
            </p>

            <div class="relative mx-auto mt-10 max-w-md">
                {{-- Estante con cajas que caen una por una --}}
                <svg viewBox="0 0 420 300" class="w-full drop-shadow-2xl" role="img" aria-label="Estante de almacén con cajas de productos">
                    {{-- Estructura --}}
                    <rect x="20" y="36" width="8" height="252" rx="4" fill="#ffffff" fill-opacity="0.25" />
                    <rect x="392" y="36" width="8" height="252" rx="4" fill="#ffffff" fill-opacity="0.25" />

                    @foreach ($shelves as $shelfY => $boxes)
                        <rect x="20" y="{{ $shelfY }}" width="380" height="8" rx="4" fill="#ffffff" fill-opacity="0.3" />

                        @foreach ($boxes as $i => [$x, $w, $h, $color])
                            @php $delay = (($loop->parent->index * 6) + $i) * 0.09 + 0.3; @endphp
                            <g class="anim-drop" style="animation-delay: {{ $delay }}s">
                                <rect x="{{ $x }}" y="{{ $shelfY - $h }}" width="{{ $w }}" height="{{ $h }}" rx="5" fill="{{ $color }}" />
                                {{-- Cinta y etiqueta --}}
                                <rect x="{{ $x + $w / 2 - 4 }}" y="{{ $shelfY - $h }}" width="8" height="{{ $h }}" fill="#ffffff" fill-opacity="0.3" />
                                <rect x="{{ $x + 7 }}" y="{{ $shelfY - $h * 0.45 }}" width="{{ max($w / 2 - 12, 8) }}" height="7" rx="2" fill="#ffffff" fill-opacity="0.55" />
                            </g>
                        @endforeach
                    @endforeach
                </svg>

                {{-- Tarjetas flotantes --}}
                <div class="anim-float absolute -left-10 top-4 flex items-center gap-3 rounded-xl bg-white/10 px-3.5 py-2.5 text-white shadow-xl ring-1 ring-white/20 backdrop-blur-md">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500/80">
                        <x-icon name="arrow-down" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-xs font-semibold">Entrada registrada</p>
                        <p class="text-xs text-slate-300">+20 unidades</p>
                    </div>
                </div>

                <div class="anim-float-slow absolute -right-8 top-28 flex items-center gap-3 rounded-xl bg-white/10 px-3.5 py-2.5 text-white shadow-xl ring-1 ring-white/20 backdrop-blur-md" style="animation-delay: -3s">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500/80">
                        <x-icon name="exclamation" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-xs font-semibold">Alerta de stock bajo</p>
                        <p class="text-xs text-slate-300">Reabastecer pronto</p>
                    </div>
                </div>

                <div class="anim-float absolute -bottom-5 left-8 flex items-center gap-3 rounded-xl bg-white/10 px-3.5 py-2.5 text-white shadow-xl ring-1 ring-white/20 backdrop-blur-md" style="animation-delay: -1.5s">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500/80">
                        <x-icon name="arrow-up" class="h-4 w-4" />
                    </span>
                    <div>
                        <p class="text-xs font-semibold">Venta en mostrador</p>
                        <p class="text-xs text-slate-300">−5 unidades</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carrusel de funciones --}}
        <div class="relative z-10 mt-14"
             x-data="{
                 i: 0,
                 items: [
                     { icon: 'arrows', title: 'Movimientos con trazabilidad', text: 'Entradas, salidas y ajustes, siempre con el usuario y la fecha.' },
                     { icon: 'exclamation', title: 'Alertas de stock bajo', text: 'Detecta a tiempo lo que necesitas reabastecer.' },
                     { icon: 'chart', title: 'Reportes en PDF y CSV', text: 'Filtra por fecha, producto, categoría y tipo.' },
                 ],
                 init() { setInterval(() => { this.i = (this.i + 1) % this.items.length }, 4500) }
             }">
            <div class="relative h-20">
                <template x-for="(item, idx) in items" :key="idx">
                    <div x-show="i === idx"
                         x-transition:enter="transition duration-500 ease-out"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition duration-300 ease-in absolute"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0 flex items-start gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-indigo-200 ring-1 ring-white/20">
                            <template x-if="item.icon === 'arrows'"><x-icon name="arrows" class="h-5 w-5" /></template>
                            <template x-if="item.icon === 'exclamation'"><x-icon name="exclamation" class="h-5 w-5" /></template>
                            <template x-if="item.icon === 'chart'"><x-icon name="chart" class="h-5 w-5" /></template>
                        </span>
                        <div>
                            <p class="font-semibold text-white" x-text="item.title"></p>
                            <p class="mt-0.5 text-sm text-slate-300" x-text="item.text"></p>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-3 flex gap-2">
                <template x-for="(item, idx) in items" :key="idx">
                    <button type="button" @click="i = idx" :aria-label="'Ir a ' + item.title"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="i === idx ? 'w-8 bg-indigo-400' : 'w-3 bg-white/25 hover:bg-white/40'"></button>
                </template>
            </div>
        </div>
    </aside>

    {{-- ============ PANEL DERECHO: formulario ============ --}}
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 px-5 py-12 sm:px-10">

        {{-- Decoración suave (visible sobre todo en móvil) --}}
        <div class="anim-blob absolute -right-20 -top-20 h-72 w-72 rounded-full bg-indigo-200/60 blur-3xl"></div>
        <div class="anim-blob absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-violet-200/50 blur-3xl" style="animation-delay: -8s"></div>

        <div class="relative z-10 w-full max-w-md">

            {{-- Marca (solo móvil y tablet) --}}
            <div class="anim-fade-up mb-8 flex flex-col items-center text-center lg:hidden">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/30">
                    <x-icon name="cube" class="h-8 w-8" />
                </span>
                <h1 class="mt-3 text-xl font-bold text-slate-800">{{ config('app.name') }}</h1>
            </div>

            <div class="anim-fade-up rounded-3xl bg-white p-8 shadow-xl shadow-slate-200/70 ring-1 ring-slate-200/70 sm:p-10 {{ $errors->any() ? 'anim-shake' : '' }}"
                 style="animation-delay: 0.1s">

                <h2 class="text-2xl font-bold text-slate-800">¡Bienvenido de nuevo!</h2>
                <p class="mb-8 mt-1 text-sm text-slate-500">Inicia sesión para administrar tu inventario.</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3.5 text-sm text-red-700" role="alert">
                        <x-icon name="exclamation" class="mt-0.5 h-5 w-5 shrink-0" />
                        <p>{{ $errors->first('email') ?: $errors->first() }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5"
                      x-data="{ loading: false, show: false, email: @js(old('email', '')), password: '' }"
                      @submit="loading = true"
                      @pageshow.window="loading = false">
                    @csrf

                    {{-- Correo --}}
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Correo electrónico</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <x-icon name="envelope" class="h-5 w-5" />
                            </span>
                            <input id="email" name="email" type="email" x-model="email" required autofocus
                                   autocomplete="username" placeholder="tucorreo@ejemplo.com"
                                   class="block w-full rounded-xl border-slate-300 py-3 pl-11 text-sm shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500
                                          {{ $errors->has('email') ? 'border-red-400' : '' }}">
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Contraseña</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <x-icon name="lock-closed" class="h-5 w-5" />
                            </span>
                            <input id="password" name="password" x-model="password" required
                                   :type="show ? 'text' : 'password'" autocomplete="current-password" placeholder="••••••••"
                                   class="block w-full rounded-xl border-slate-300 py-3 pl-11 pr-12 text-sm shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="button" @click="show = !show"
                                    :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-slate-600">
                                <span x-show="!show"><x-icon name="eye" class="h-5 w-5" /></span>
                                <span x-show="show" x-cloak><x-icon name="eye-slash" class="h-5 w-5" /></span>
                            </button>
                        </div>
                    </div>

                    <label for="remember_me" class="inline-flex cursor-pointer items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                               class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-slate-600">Mantener sesión iniciada</span>
                    </label>

                    {{-- Botón con estado de carga --}}
                    <button type="submit" :disabled="loading"
                            class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 transition hover:shadow-xl hover:shadow-indigo-600/40 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-80">
                        <span class="absolute inset-0 -translate-x-full bg-white/20 transition-transform duration-700 group-hover:translate-x-full"></span>

                        <svg x-show="loading" x-cloak class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                        </svg>
                        <span class="relative" x-text="loading ? 'Ingresando...' : 'Ingresar al sistema'">Ingresar al sistema</span>
                    </button>

                    {{-- Accesos de demostración: solo aparecen en entorno local --}}
                    @env('local')
                        <div class="border-t border-dashed border-slate-200 pt-4">
                            <p class="mb-2 text-center text-xs font-medium uppercase tracking-wide text-slate-400">Acceso rápido (modo local)</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" @click="email = 'admin@inventario.test'; password = 'Admin12345'"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                                    Administrador
                                </button>
                                <button type="button" @click="email = 'usuario@inventario.test'; password = 'Usuario12345'"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                                    Usuario
                                </button>
                            </div>
                        </div>
                    @endenv
                </form>
            </div>

            <p class="anim-fade-up mt-6 text-center text-xs text-slate-400" style="animation-delay: 0.3s">
                © {{ date('Y') }} {{ config('app.name') }} · Proyecto universitario
            </p>
        </div>
    </main>
</div>
</body>
</html>