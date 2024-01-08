<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;


class ProductCategoryModel extends Model
{
    use HasFactory;
    protected $table='product_category';
    protected $fillable = ['company_id','category_name'];



    public function companyname()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

}