<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;

class BuyercompanyExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $buyer = User::with('company')->select('id','name','email','user_type','blocked')->whereHas(
            'role', function($q){
                $q->where('name', 'general');
            })->get()
        ->map(function($buyer){
            return [
                'name'=>$buyer->name,
                'email'=>$buyer->email,
                'user_type'=>$buyer->user_type,
                'company_name'=>(isset($buyer->company[0]->company_name) && $buyer->company[0]->company_name!='') ? $buyer->company[0]->company_name : '' ,
                'blocked'=>($buyer->blocked==1) ? 'UnBlocked' : 'Blocked',
            ];
        });
        return $buyer;
    }

    public function headings(): array
    {
        return ['Name','Email','User Type','Company Name','Blocked Status'];
    }
}
