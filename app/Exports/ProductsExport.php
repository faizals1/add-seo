<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Products;

class ProductsExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $product = Products::with(['company','category','subcategory'])->select('id','product_code','product_name','description','product_slug','product_catid','product_subcatid','food_type','blocked',"created_at","updated_at")->get()->map(function($product){
            return [
                'product_code'=>$product->product_code,
                'product_name'=>$product->product_name,
                'description'=>$product->description,
                'product_slug'=>$product->product_slug,
                'product_category'=>(isset($product->category)) ? $product->category->category_name : '',
                'product_subcategory'=>(isset($product->subcategory)) ? $product->subcategory->subcat_name : '',
                'food_type'=>$product->food_type,
                'product_type'=>$product->product_type,
                'company_name'=>(isset($product->company[0]->company_name) && $product->company[0]->company_name!='') ? $product->company[0]->company_name : '' ,
                'blocked'=>$product->blocked,
                'created_at'=>$product->created_at,
            ];
        });
        return $product;
    }

    public function headings(): array
    {
        return ['Product Code','Product Name','Description','Slug','Product Category','Product Subcategory','Food Type','Company Name','Blocked',"Created date",];
    }
}
