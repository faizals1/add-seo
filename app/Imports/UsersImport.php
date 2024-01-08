<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use App\Models\{User,Role};
use App\Models\Company;
use App\Models\CompanyUserMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;


class UsersImport implements ToCollection,WithStartRow
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) 
        {
            
            if(count($row)==12){
                $role = Role::where('name', $row[10])->first();
                $company = Company::where('id', $row[11])->first();
                
                if(!User::where('email', '=', $row[1])->exists() && (isset($company) && $company->id && $company->id>0)) {
                    $users = User::create([
                        'name'     => $row[0],
                        'email'    => $row[1], 
                        'password' => Hash::make($row[2]),
                        'phone' => $row[3],
                        'address' => $row[4],
                        'country' => (is_int($row[5])) ? $row[5] : 11,
                        'state' => (is_int($row[6])) ? $row[6] : 13,
                        'city' => (is_int($row[7])) ? $row[7] : 5,
                        'wedsite_url' => $row[8],                
                        'blocked' => (is_int($row[9])) ? $row[9] : 1,                
                    ]);
                    if(isset($company) && $company->id && $company->id>0){
                        $users->company()->attach($company->id);
                    }
                    if(isset($role) && $role->id && $role->id>0){
                        $users->role()->attach( $role->id);
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }

}
