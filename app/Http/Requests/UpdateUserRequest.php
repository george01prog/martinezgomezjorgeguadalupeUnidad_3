<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'string', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var User $target */
            $target = $this->route('user');
            $newRole = $this->input('role');
            $newActive = $this->boolean('is_active');

            // Un administrador no puede modificar su propio rol ni desactivarse
            if ($target->is($this->user())) {
                if ($newRole !== $target->role) {
                    $validator->errors()->add('role', 'No puedes cambiar tu propio rol.');
                }

                if (! $newActive) {
                    $validator->errors()->add('is_active', 'No puedes desactivar tu propia cuenta.');
                }
            }

            // Siempre debe quedar al menos un administrador activo
            if ($target->isLastActiveAdmin() && ($newRole !== User::ROLE_ADMIN || ! $newActive)) {
                $validator->errors()->add('role', 'Debe existir al menos un administrador activo.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'role' => 'rol',
            'is_active' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role.required' => 'Selecciona un rol.',
            'role.in' => 'El rol seleccionado no es válido.',
            'is_active.required' => 'Selecciona el estado del usuario.',
        ];
    }
}