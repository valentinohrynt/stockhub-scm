<?php

namespace App\Models;

use App\Models\RawMaterial;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'slug',
        'is_active',
    ];

    public function rawMaterial()
    {
        return $this->hasMany(RawMaterial::class);
    }

    protected static function booted()
    {
        static::creating(function ($supplier) {
            $supplier->slug = static::generateUniqueSlug($supplier->name);
        });

        static::updating(function ($supplier) {
            if ($supplier->isDirty('name')) {
                $supplier->slug = static::generateUniqueSlug($supplier->name, $supplier->id);
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
