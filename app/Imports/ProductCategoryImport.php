<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\{ProductCategoryModel,ProductSubCategory,Company};
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

use Helper;

class ProductCategoryImport implements ToCollection, WithStartRow, WithHeadingRow
{
    use Importable;
    
    private $insertrows = 0;
    private $status_message = [];
    
    public function startRow(): int
    {
        return 2;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        Validator::make($collection->toArray(), [
            '*.company_id' => 'required|numeric',
            '*.category_name' => 'required|max:190'
        ])->validate();

        $productCategory = [];
        $userType = auth()->user()->role()->first()->name;
        $companyIds = Company::pluck('id')->toArray();
        foreach ($collection as $row) 
        {
            if(count($row)==2)
            {
                if($userType!=config('custom.superadminrole'))
                {
                    $company_id = Helper::loginUserCompanyId();
                }else{
                    $company_id = $row['company_id'];
                }
                if(in_array($company_id,$companyIds)){
                    
                    $checkCatgoryName = ProductCategoryModel::where('company_id',$company_id)->where('category_name','like',$row['category_name']);
                    if($checkCatgoryName->count()==0)
                    {
                        $productCategory[] = [
                            'company_id'     => $company_id,
                            'category_name'    => $row['category_name'],
                        ];
                    }else{
                        $this->status_message[] = $row['category_name'].' '.__('locale.name_exits');
                    }
                }else{
                    $this->status_message[] = __('locale.Company').' id '.$row['company_id'].' '.__('locale.import_company_errormsg');
                }
            }
        }
        
        if(!empty($productCategory))
        {
            ProductCategoryModel::insert($productCategory);
            ++$this->rows;
        }
    }

    public function getRowCount(): int
    {
        return $this->insertrows;
    }

    public function getErrorMessage()
    {
        return $this->status_message;
    }
}
