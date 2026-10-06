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

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <x-input-label for="sku" value="Código SKU *" />
        <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full uppercase"
                      :value="old('sku', $product->sku)" maxlength="50" required autofocus />
        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category_id" value="Categoría *" />
        <select id="category_id" name="category_id" required
                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Selecciona una categoría</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="name" value="Nombre *" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $product->name)" maxlength="150" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" value="Descripción" />
        <textarea id="description" name="description" rows="3" maxlength="1000"
                  class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="purchase_price" value="Precio de compra *" />
        <div class="relative mt-1">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">$</span>
            <input id="purchase_price" name="purchase_price" type="number" step="0.01" min="0" required
                   value="{{ old('purchase_price', $product->purchase_price) }}"
                   class="block w-full rounded-lg border-slate-300 pl-7 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <x-input-error :messages="$errors->get('purchase_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="sale_price" value="Precio de venta *" />
        <div class="relative mt-1">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">$</span>
            <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" required
                   value="{{ old('sale_price', $product->sale_price) }}"
                   class="block w-full rounded-lg border-slate-300 pl-7 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
    </div>

    <div>
        @if ($product->exists)
            <x-input-label value="Stock actual" />
            <div class="mt-1 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5">
                <span class="text-sm font-semibold text-slate-800">{{ $product->stock }}</span>
                <x-stock-badge :product="$product" />
            </div>
            <p class="mt-1 text-xs text-slate-500">El stock solo cambia mediante movimientos de inventario.</p>
        @else
            <x-input-label for="stock" value="Stock inicial *" />
            <input id="stock" name="stock" type="number" step="1" min="0" required
                   value="{{ old('stock', $product->stock) }}"
                   class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
        @endif
    </div>

    <div>
        <x-input-label for="min_stock" value="Stock mínimo *" />
        <input id="min_stock" name="min_stock" type="number" step="1" min="0" required
               value="{{ old('min_stock', $product->min_stock) }}"
               class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs text-slate-500">Se avisará de stock bajo cuando el stock sea menor o igual a este valor.</p>
        <x-input-error :messages="$errors->get('min_stock')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <input type="hidden" name="is_active" value="0">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1"
                   @checked((bool) old('is_active', $product->is_active))
                   class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="text-sm font-medium text-slate-700">Producto activo</span>
        </label>
        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
    </div>
</div>