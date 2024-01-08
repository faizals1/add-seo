<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\{ProductCategoryModel,ProductSubCategory};
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\BuyerTypeChannel;

use Helper;

class BuyerTypeChannelImport implements ToCollection, WithStartRow
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
        // dd($collection);
        $buyertypechannel = [];
        $userType = auth()->user()->role()->first()->name;
        foreach ($collection as $row) 
        {
            if(count($row)==3){
                if($userType!=config('custom.superadminrole')){
                    $company_id = Helper::loginUserCompanyId();
                }else{
                    $company_id = $row[1];
                }

                $buyertypechannelName = BuyerTypeChannel::where('company_id',$company_id)->where('name','like',$row[1]);
                
                
                if($buyertypechannelName->count()==0){
                    $buyertypechannel[] = [
                        'company_id'  => $company_id,
                        'name'        => $row[2]
                    ];
                }
            }
        }
        // echo '<pre>'; print_r($buyertypechannel); die;
        if(!empty($buyertypechannel)){
            BuyerTypeChannel::insert($buyertypechannel);
        }
    }
}
