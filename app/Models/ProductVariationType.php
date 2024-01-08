<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;


class ProductVariationType extends Model
{
    use HasFactory;
    protected $table='product_variation_type';
    protected $fillable = ['company_id','name'];

    public function companyname()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }


}

