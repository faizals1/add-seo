<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ProductVariationType;

class AdminProductvariationTypeExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ProductVariationType::select('id','company_id','name')->get();
    }

    public function headings(): array
    {
        return ['Id','Company Id','Name',];
    }
}
