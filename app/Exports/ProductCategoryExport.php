<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ProductCategoryModel;
use Helper;

class ProductCategoryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $company_id = Helper::loginUserCompanyId();

        return ProductCategoryModel::select('id','company_id','category_name')->where('company_id',$company_id)->get();
    }

    public function headings(): array
    {
        return ['Category Id','Company Id','Category Name',];
    }
}
