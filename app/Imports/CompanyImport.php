<?php

namespace App\Imports;

use App\Models\Company;
// use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CompanyImport implements ToCollection,WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            if(count($row)==13){
                Company::create([
                    
                    'company_name'     => $row[0],
                    'company_code'    => $row[1], 
                    'address1' => $row[2],
                    'address2' => $row[3],
                    'state' => $row[4],
                    'city' => $row[5],
                    'pincode' => $row[6],
                    'country' => $row[7],
                    'contact_person' => $row[8],
                    'contact_mobile' => $row[9],
                    'phone_no' => $row[10],
                    'licence_valid_till' => date('Y-m-d',strtotime($row[11])),
                    'blocked' => (is_int($row[12])) ? $row[12] : 1,
                    
                ]);
            }
        }
    }
}
