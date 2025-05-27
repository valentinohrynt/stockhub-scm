<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function rawMaterial()
    {
        return $this->hasMany(RawMaterial::class);
    }
}
