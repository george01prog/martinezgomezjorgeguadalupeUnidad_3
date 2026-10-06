<?php

namespace App\Http\Requests;

use App\Models\InventoryMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required', 'integer',
                // exists respeta soft deletes solo si se indica deleted_at
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'type' => ['required', Rule::in(array_keys(InventoryMovement::TYPES))],
            'quantity' => [
                'required', 'integer',
                // Entrada y salida: mínimo 1. Ajuste: puede ser 0 (inventario en cero)
                $this->input('type') === InventoryMovement::TYPE_ADJUST ? 'min:0' : 'min:1',
                'max:1000000',
            ],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'producto',
            'type' => 'tipo de movimiento',
            'quantity' => 'cantidad',
            'reason' => 'motivo',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Selecciona un producto.',
            'product_id.exists' => 'El producto seleccionado no existe.',
            'type.required' => 'Selecciona el tipo de movimiento.',
            'type.in' => 'El tipo de movimiento no es válido.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser mayor a cero (en un ajuste puede ser 0).',
            'quantity.max' => 'La cantidad no puede superar 1,000,000.',
            'reason.max' => 'El motivo no puede superar los 500 caracteres.',
        ];
    }
}