@foreach (['success' => session('success'), 'error' => session('error')] as $type => $message)
    @if ($message)
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition
             role="alert"
             class="mb-6 flex items-start gap-3 rounded-xl border p-4 text-sm {{ $type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}">
            <x-icon :name="$type === 'success' ? 'check-circle' : 'exclamation'" class="h-5 w-5 shrink-0" />
            <p class="flex-1">{{ $message }}</p>
            <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Cerrar">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>
    @endif
@endforeach