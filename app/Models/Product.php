<?php

namespace App\Models;

use App\Models\Category;
use App\Models\RawMaterial;
use Illuminate\Support\Str;
use App\Models\BillOfMaterial;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'description',
        'base_price',
        'selling_price',
        'stock',
        'category_id',
        'image_path',
        'slug',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function billOfMaterial()
    {
        return $this->hasMany(BillOfMaterial::class);
    }

    public function rawMaterial()
    {
        return $this->belongsToMany(RawMaterial::class, 'bill_of_materials')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug = static::generateUniqueSlug($product->name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    protected static function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
