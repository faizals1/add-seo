<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductCategoryModel;
use App\Models\Company;


class ProductSubCategory extends Model
{
    use HasFactory;
    protected $fillable = ['procat_id','subcat_name'];

    public function categoryname()
    {
        return $this->belongsTo(ProductCategoryModel::class, 'procat_id', 'id')->with('companyname');
    }
    
}
