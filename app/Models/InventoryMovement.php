<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    public const TYPE_IN = 'entrada';
    public const TYPE_OUT = 'salida';
    public const TYPE_ADJUST = 'ajuste';

    public const TYPES = [
        self::TYPE_IN => 'Entrada',
        self::TYPE_OUT => 'Salida',
        self::TYPE_ADJUST => 'Ajuste',
    ];

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        // withTrashed: el historial sigue mostrando productos eliminados con soft delete
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_IN => 'blue',
            self::TYPE_OUT => 'red',
            default => 'violet',
        };
    }

    /** Diferencia real de stock (positiva, negativa o cero). */
    public function getDifferenceAttribute(): int
    {
        return $this->stock_after - $this->stock_before;
    }
}