<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ProductSubCategory;
use Helper;


class SubCategoryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $company_id = Helper::loginUserCompanyId();

        return ProductSubCategory::select('id','procat_id','subcat_name')->whereHas('categoryname', function ($query) use ($company_id) {
           $query->where('company_id', $company_id);

       })->get();

    }

    public function headings(): array
    {
        return ['Sub Category Id','Category Id','Sub Category Name'];
    }
}
