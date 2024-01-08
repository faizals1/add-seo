<?php

namespace App\Exports;
use App\Models\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select("users.name","email","phone","address","country","state","city","website_url","image","blocked","created_at","updated_at")->whereHas(
            'role', function($q){
                $q->where('name', 'company-user');
            }
        )->get();
    }

    public function headings(): array
    {
        return ["Name","Email","Phone","Address","Country","State","City","Website Url","Image","Blocked","Created date","Updated date"
    ];
    }
}
