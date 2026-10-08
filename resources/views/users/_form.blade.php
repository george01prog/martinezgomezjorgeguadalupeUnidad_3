@php
    $isEdit = $user->exists;
    $isSelf = $isEdit && $user->id === auth()->id();
@endphp

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
        <x-input-label for="name" value="Nombre *" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $user->name)" maxlength="100" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" value="Correo electrónico *" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                      :value="old('email', $user->email)" maxlength="150" required autocomplete="off" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password" :value="$isEdit ? 'Nueva contraseña' : 'Contraseña *'" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                      autocomplete="new-password" :required="! $isEdit" />
        <p class="mt-1 text-xs text-slate-500">
            {{ $isEdit ? 'Déjala vacía para conservar la actual. ' : '' }}Mínimo 8 caracteres.
        </p>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="$isEdit ? 'Confirmar nueva contraseña' : 'Confirmar contraseña *'" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                      class="mt-1 block w-full" autocomplete="new-password" :required="! $isEdit" />
    </div>

    @if ($isSelf)
        {{-- El administrador no puede cambiar su propio rol ni desactivarse --}}
        <div>
            <x-input-label value="Rol" />
            <div class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5">
                <x-badge color="indigo">{{ $user->role_label }}</x-badge>
            </div>
            <input type="hidden" name="role" value="{{ $user->role }}">
            <p class="mt-1 text-xs text-slate-500">No puedes cambiar tu propio rol.</p>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div>
            <x-input-label value="Estado" />
            <div class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5">
                <x-badge color="green">Activo</x-badge>
            </div>
            <input type="hidden" name="is_active" value="1">
            <p class="mt-1 text-xs text-slate-500">No puedes desactivar tu propia cuenta.</p>
        </div>
    @else
        <div>
            <x-input-label for="role" value="Rol *" />
            <select id="role" name="role" required
                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="user" @selected(old('role', $user->role) === 'user')>Usuario</option>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrador</option>
            </select>
            <p class="mt-1 text-xs text-slate-500">El administrador tiene acceso completo al sistema.</p>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="flex items-center md:pt-6">
            <div>
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1"
                           @checked((bool) old('is_active', $user->is_active))
                           class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="text-sm font-medium text-slate-700">Usuario activo</span>
                </label>
                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
            </div>
        </div>
    @endif
</div>