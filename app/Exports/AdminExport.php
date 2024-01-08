<?php

namespace App\Exports;
use App\Models\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with('company')->select("id","users.name","email","phone","address","country","state","city","website_url","image","blocked","created_at","updated_at")->whereHas(
            'role', function($q){
                $q->where('name', 'company-admin');
            }
        )->get();
    }

    public function headings(): array
    {
        return ["id","name","email","phone","address","country","state","city","website_url","image","blocked","created","updated","company name"
    ];
    }
}
