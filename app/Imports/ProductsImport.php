<?php

namespace App\Imports;

use App\Models\Products;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Helper;

class ProductsImport implements ToCollection,WithStartRow,WithHeadingRow
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
        // dd($rows->toArray());
        Validator::make($rows->toArray(), [
            '*.product_code' => 'required',
            '*.product_name' => 'required',
            '*.product_category' => 'required|numeric',
            '*.product_type' => 'required',
            '*.food_type' => 'required',
        ])->validate();

        $userType = auth()->user()->role()->first()->name;
        foreach ($rows as $row) 
        {
            if(count($row)==9){
                $product = Products::create([
                    
                    'product_code'     => $row['product_code'],
                    'product_name'    => $row['product_name'], 
                    'product_slug' => $row['slug'],
                    'description' => $row['description'],
                    'product_catid' => $row['product_category'],
                    'product_subcatid' => $row['product_subcategory'],
                    'food_type' => ($row['food_type']!='' && in_array($row['food_type'],array('veg','non-veg'))) ? $row['food_type'] : 'veg',
                    'product_type' => ($row['product_type']!='' && in_array($row['product_type'],array('domestic','foreign'))) ? $row['product_type'] : 'domestic',
                    'blocked' => (is_int($row['blocked'])) ? $row['blocked'] : 1,
                    
                ]);
                if($userType!=config('custom.superadminrole')){
                    $product->company()->attach(Helper::loginUserCompanyId());
                }
                // dd($product); exit();
            }

        }
    }
}
