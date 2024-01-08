<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ProductSubCategory;

class AdminSubCategoryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ProductSubCategory::select('id','procat_id','subcat_name')->get();
    }

    public function headings(): array
    {
        return ['Sub Category Id','Category Id','Sub Category Name',];
    }
}
