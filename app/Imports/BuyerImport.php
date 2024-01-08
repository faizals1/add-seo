<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\{User,Role};
use Illuminate\Support\Str;
use Helper;

class BuyerImport implements ToCollection,WithHeadingRow,WithStartRow
{
    use Importable;
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        $row_count_validation = 5;
        $userType = auth()->user()->role()->first()->name;
        if($userType!=config('custom.superadminrole')){
            $company_id = Helper::loginUserCompanyId();
            $row_count_validation = 4;
        }
        Validator::make($collection->toArray(), [
            '*.name' => 'required',
            '*.email' => 'required|unique:users|max:250',
            '*.user_type' => 'required'
        ])->validate();

        if($userType==config('custom.superadminrole')){
            Validator::make($collection->toArray(), [
                '*.company_id' => 'required|numeric'
            ])->validate();
        }

        $role = Role::where('name', 'general')->first();
        $buyerType = config('custom.buyerTypeArray');
        
        foreach ($collection as $row) 
        {
            
            if(count($row)==$row_count_validation){
                $company_id = (isset($row['company_id'])) ? $row['company_id'] : $company_id;
                $insertrow['password'] = 'null';
                $insertrow['name'] = $row['name'];
                $insertrow['email'] = $row['email'];
                $insertrow['user_type'] = (in_array($row['user_type'],$buyerType)) ? $row['user_type'] : 'domestic';
                $insertrow['invite_code'] = Str::random(8);
                
                $buyer = User::create($insertrow);
                $buyer->company()->attach($company_id);
                $buyer->role()->attach( $role->id);
            }
        }

    }
}
