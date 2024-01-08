<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
use Helper;

class BuyerExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $company_id = Helper::loginUserCompanyId();

        $buyer = User::select('name','email','user_type','blocked')->whereHas(
            'role', function($q){
                $q->where('name', 'general');
            })->whereHas('company',function($query) use($company_id){
            $query->where('id',$company_id);
        })->get()->map(function($buyer){
            return [
                'name'=>$buyer->name,
                'email'=>$buyer->email,
                'user_type'=>$buyer->user_type,
                'blocked'=>($buyer->blocked==1) ? 'UnBlocked' : 'Blocked',
            ];
        });
        
        return $buyer;
    }

    public function headings(): array
    {
        return ['Name','Email','User Type','Blocked Status'];
    }

}
