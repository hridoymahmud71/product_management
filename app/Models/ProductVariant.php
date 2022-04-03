<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{

    public function main_variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }

}
