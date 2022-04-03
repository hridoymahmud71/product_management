<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function product_variant_prices()
    {
        return $this->hasMany(ProductVariantPrice::class);
    }

    //accessor
    public function getVARIANTDATAAttribute()
    {
        $variations = $this->product_variant_prices()->with(['variant_one', 'variant_two', 'variant_three'])->get();
        $variation_parent_array = [];
        foreach ($variations as  $variation) {

            $variation_array = [];
            $variation_data = [];
            $variation_string = "";

            if ($variation->variant_one != null) {
                $variation_array[] = $variation->variant_one->variant;
            }

            if ($variation->variant_two != null) {
                $variation_array[] = $variation->variant_two->variant;
            }

            if ($variation->variant_three != null) {
                $variation_array[] = $variation->variant_three->variant;
            }

            if (!empty($variation_array)) {
                $variation_string = implode(" / ", $variation_array);
            }
            $variation_data['id']       = $variation->id;
            $variation_data['string']   = $variation_string;
            $variation_data['price']    = $variation->price;
            $variation_data['stock']    = $variation->stock;

            $variation_parent_array[]   = $variation_data;
        }
        return $variation_parent_array;
        
    }

}
