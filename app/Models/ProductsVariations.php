<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariationType;

class ProductsVariations extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','name','sku','main_price','offer_price','quantity','variation_type'];

    
    public function productvariationName()
    {
    return $this->hasOne(ProductVariationType::class, 'id', 'variation_type');
    }

}


