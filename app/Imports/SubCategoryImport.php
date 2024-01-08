<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\{ProductCategoryModel,ProductSubCategory};
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Helper;

class SubCategoryImport implements ToCollection, WithStartRow, WithHeadingRow
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
        Validator::make($collection->toArray(), [
            '*.procat_id' => 'required|numeric',
            '*.subcat_name' => 'required'
        ])->validate();

        $subCategory = [];
        $userType = auth()->user()->role()->first()->name;
        if($userType!=config('custom.superadminrole')){
            $company_id = Helper::loginUserCompanyId();
            $category_array = ProductCategoryModel::where('company_id',$company_id)->pluck('id')->toArray();
        }
        foreach ($collection as $row) 
        {
            
            if(count($row)==2){
                $category_id = $row['procat_id'];
                
                if(isset($category_array) && in_array($category_id,$category_array)){
                    $checkCatgory = ProductCategoryModel::where('id',$category_id);
                    
                    if($checkCatgory->count()>0){
                        $checkSubCatgory = ProductSubCategory::where('procat_id',$category_id)->where('subcat_name','like',$row['subcat_name']);
                        if($checkSubCatgory->count()==0){
                            $subCategory[] = [
                                'procat_id'     => $category_id,
                                'subcat_name'    => $row['subcat_name'],
                            ];
                            
                        }
                    }
                }else{
                    $checkCatgory = ProductCategoryModel::where('id',$category_id);
                    if($checkCatgory->count()>0){
                        $checkSubCatgory = ProductSubCategory::where('procat_id',$category_id)->where('subcat_name','like',$row['subcat_name']);
                        if($checkSubCatgory->count()==0){
                            $subCategory[] = [
                                'procat_id'     => $category_id,
                                'subcat_name'    => $row['subcat_name'],
                            ];
                            
                        }
                    }
                }
            }
        }
        
        if(!empty($subCategory)){
            return ProductSubCategory::insert($subCategory);
        }else{
            return redirect()->back()->with('error','try again');
        }
    }
}
