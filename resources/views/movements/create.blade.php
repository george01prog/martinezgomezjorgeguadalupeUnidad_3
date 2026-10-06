<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-800">Registrar movimiento</h1>
        <p class="text-sm text-slate-500">Entrada, salida o ajuste de existencias</p>
    </x-slot>

    @php
        $productData = $products->mapWithKeys(fn ($p) => [$p->id => $p->stock])->all();
    @endphp

    <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
         x-data="{
             stocks: @js($productData),
             productId: '{{ old('product_id', $selectedProduct) }}',
             type: '{{ old('type', 'entrada') }}',
             quantity: '{{ old('quantity') }}',
             get stock() { return this.productId ? (this.stocks[this.productId] ?? null) : null },
             get qty() { return this.quantity === '' ? null : parseInt(this.quantity) },
             get result() {
                 if (this.stock === null || this.qty === null || isNaN(this.qty)) return null;
                 if (this.type === 'entrada') return this.stock + this.qty;
                 if (this.type === 'salida') return this.stock - this.qty;
                 return this.qty;
             }
         }">

        <form method="POST" action="{{ route('movements.store') }}">
            @csrf

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-semibold">Corrige los siguientes errores:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-5">
                {{-- Producto --}}
                <div>
                    <x-input-label for="product_id" value="Producto *" />
                    <select id="product_id" name="product_id" x-model="productId" required
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Selecciona un producto</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->sku }} · {{ $product->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />

                    <p x-show="stock !== null" x-cloak class="mt-2 text-sm text-slate-500">
                        Stock actual: <span class="font-semibold text-slate-800" x-text="stock"></span> unidades
                    </p>
                </div>

                {{-- Tipo --}}
                <div>
                    <x-input-label value="Tipo de movimiento *" />
                    <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        @foreach ([
                            ['entrada', 'Entrada', 'Suma unidades al stock', 'arrow-down', 'blue'],
                            ['salida', 'Salida', 'Resta unidades del stock', 'arrow-up', 'red'],
                            ['ajuste', 'Ajuste', 'Fija el stock contado', 'adjust', 'violet'],
                        ] as [$value, $label, $hint, $icon, $color])
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="{{ $value }}" x-model="type" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50
                                            peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500/30 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">
                                    <div class="flex items-center gap-2">
                                        <x-icon :name="$icon" class="h-5 w-5 {{ $color === 'blue' ? 'text-blue-600' : ($color === 'red' ? 'text-red-600' : 'text-violet-600') }}" />
                                        <span class="text-sm font-semibold text-slate-800">{{ $label }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                {{-- Cantidad --}}
                <div>
                    <x-input-label for="quantity">
                        <span x-text="type === 'ajuste' ? 'Nuevo stock contado *' : 'Cantidad *'"></span>
                    </x-input-label>
                    <input id="quantity" name="quantity" type="number" step="1" min="0" required x-model="quantity"
                           class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />

                    <div x-show="result !== null" x-cloak
                         class="mt-3 rounded-lg px-3 py-2 text-sm"
                         :class="result < 0 ? 'bg-red-50 text-red-700' : 'bg-slate-50 text-slate-600'">
                        <template x-if="result < 0">
                            <span>La salida supera el stock disponible.</span>
                        </template>
                        <template x-if="result >= 0">
                            <span>Stock resultante: <span class="font-semibold text-slate-800" x-text="result"></span> unidades</span>
                        </template>
                    </div>
                </div>

                {{-- Motivo --}}
                <div>
                    <x-input-label for="reason" value="Motivo / observaciones" />
                    <textarea id="reason" name="reason" rows="3" maxlength="500"
                              class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>

                <p class="text-xs text-slate-500">
                    Este movimiento quedará registrado a nombre de <span class="font-medium">{{ auth()->user()->name }}</span>.
                </p>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="{{ route('movements.index') }}"
                   class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Cancelar
                </a>
                <x-primary-button>Registrar movimiento</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>