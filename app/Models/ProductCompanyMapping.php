<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCompanyMapping extends Model
{
    use HasFactory;
    protected $table ='company_product_mapping';

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
