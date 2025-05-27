<?php

namespace App\Models;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\BillOfMaterial;
use Illuminate\Database\Eloquent\Model;

class BillOfMaterial extends Model
{
    //
    protected $fillable = [
        'product_id',
        'raw_material_id',
        'quantity',
        'total_cost',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function details()
    {
        return $this->hasMany(BillOfMaterial::class, 'product_id', 'product_id');
    }

    public function getPossibleUnitsAttribute()
    {
        $minUnits = PHP_INT_MAX;

        foreach ($this->details as $detail) {
            $required = $detail->quantity;
            $available = $detail->rawMaterial->stock ?? 0;

            if ($required > 0) {
                $possible = floor($available / $required);
                $minUnits = min($minUnits, $possible);
            }
        }

        return $minUnits === PHP_INT_MAX ? 0 : $minUnits;
    }
}
