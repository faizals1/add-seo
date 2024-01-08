<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Products;
use Helper;

class UserProductsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $userType = auth()->user()->role()->first()->name;
        $productResult = Products::select('product_code','product_name','description','product_slug','product_catid','product_subcatid','food_type','product_type','blocked',"created_at","updated_at");

        if($userType!=config('custom.superadminrole')){
            $company_id = Helper::loginUserCompanyId();
            $productResult = $productResult->whereHas('company',function($query) use ($company_id) {
                $query->where('company_id',$company_id);
            });
        }
        return $productResult->get();
    }

    public function headings(): array
    {
        return ['Product Code','Product Name','Description','Slug','Product Category','Product Subcategory','Food Type','Product Type','Blocked',"Created date","Updated date"];
    }
}
