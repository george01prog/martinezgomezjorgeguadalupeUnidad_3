<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'sku' => [
                'required', 'string', 'max:50',
                Rule::unique('products', 'sku')->ignore($this->route('product')),
            ],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'purchase_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'sale_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'min_stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sku' => 'código SKU',
            'name' => 'nombre',
            'category_id' => 'categoría',
            'description' => 'descripción',
            'purchase_price' => 'precio de compra',
            'sale_price' => 'precio de venta',
            'min_stock' => 'stock mínimo',
            'is_active' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'El código SKU es obligatorio.',
            'sku.unique' => 'Ya existe un producto con ese SKU.',
            'name.required' => 'El nombre del producto es obligatorio.',
            'category_id.required' => 'Selecciona una categoría.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'purchase_price.required' => 'El precio de compra es obligatorio.',
            'purchase_price.numeric' => 'El precio de compra debe ser un número válido.',
            'purchase_price.min' => 'El precio de compra no puede ser negativo.',
            'sale_price.required' => 'El precio de venta es obligatorio.',
            'sale_price.numeric' => 'El precio de venta debe ser un número válido.',
            'sale_price.min' => 'El precio de venta no puede ser negativo.',
            'min_stock.required' => 'El stock mínimo es obligatorio.',
            'min_stock.integer' => 'El stock mínimo debe ser un número entero.',
            'min_stock.min' => 'El stock mínimo no puede ser negativo.',
            'is_active.required' => 'Selecciona el estado del producto.',
        ];
    }
}