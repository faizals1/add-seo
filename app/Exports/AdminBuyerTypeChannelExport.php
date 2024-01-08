<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\BuyerTypeChannel;

class AdminBuyerTypeChannelExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return BuyerTypeChannel::select('id','company_id','name')->get();
    }

    public function headings(): array
    {
        return ['Id','Company Id','Name',];
    }
}
