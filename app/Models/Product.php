<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public const STOCK_NORMAL = 'normal';
    public const STOCK_LOW = 'low';
    public const STOCK_OUT = 'out';

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'description',
        'purchase_price',
        'sale_price',
        'stock',
        'min_stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /* ---------- Estado de stock (calculado) ---------- */

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return self::STOCK_OUT;
        }

        return $this->stock <= $this->min_stock ? self::STOCK_LOW : self::STOCK_NORMAL;
    }

    public function getStockLabelAttribute(): string
    {
        return match ($this->stock_status) {
            self::STOCK_OUT => 'Agotado',
            self::STOCK_LOW => 'Stock bajo',
            default => 'Stock normal',
        };
    }

    public function getStockColorAttribute(): string
    {
        return match ($this->stock_status) {
            self::STOCK_OUT => 'red',
            self::STOCK_LOW => 'amber',
            default => 'green',
        };
    }

    /* ---------- Scopes ---------- */

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('sku', 'like', "%{$term}%")
              ->orWhere('name', 'like', "%{$term}%");
        });
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', 0);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeNormalStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '>', 'min_stock');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }
}