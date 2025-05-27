<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialConsumptionLog extends Model
{
    //

    protected $fillable = [
        'raw_material_id',
        'transaction_detail_id',
        'quantity_used',
        'consumption_date',
    ];
}