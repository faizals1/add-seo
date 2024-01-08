<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\{ProductCategoryModel,ProductSubCategory};
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\ProductVariationType;

use Helper;

class ProductVariationTypeImport implements ToCollection, WithStartRow
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
        $productvariation = [];
        $userType = auth()->user()->role()->first()->name;
        foreach ($collection as $row) 
        {
            if(count($row)==3){
                if($userType!=config('custom.superadminrole')){
                    $company_id = Helper::loginUserCompanyId();
                }else{
                    $company_id = $row[1];
                }

                $productvariationName = ProductVariationType::where('company_id',$company_id)->where('name','like',$row[1]);
                    
                if($productvariationName->count()==0){
                    $productvariation[] = [
                        'company_id'  => $company_id,
                        'name'        => $row[2]
                    ];
                }
            }
        }
        // echo '<pre>'; print_r($productvariation); die;
        if(!empty($productvariation)){
            ProductVariationType::insert($productvariation);
        }
    }
}
