<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // La clase Product se creará en la Etapa 3.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function hasProducts(): bool
    {
        return Schema::hasTable('products') && $this->products()->exists();
    }
}