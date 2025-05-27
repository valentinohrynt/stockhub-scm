<?php

namespace App\Models;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JitNotification extends Model
{
    use HasFactory;
    protected $fillable = ['raw_material_id', 'message', 'status', 'resolved_at'];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}