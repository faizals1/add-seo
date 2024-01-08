<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ProductVariationType;
use Helper;


class ProductvariationTypeExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $company_id = Helper::loginUserCompanyId();

        return ProductVariationType::select('id','company_id','name')->whereHas('companyname', function ($query) use ($company_id) {
           $query->where('company_id', $company_id);

       })->get();

    }

    public function headings(): array
    {
        return ['Id','Company Id','Name',];
    }
}
