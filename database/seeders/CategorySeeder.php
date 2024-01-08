<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategoryModel;
use App\Models\ProductSubCategory;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $category_data = [[
            'category_name' => 'sweets'
        ]];
        $category = ProductCategoryModel::insert($category_data);
        $subcategory_data = [
            ['procat_id'=>$category->id,'subcat_name'=>'sonahalwa']
        ];
        ProductSubCategory::insert($subcategory_data);
    }
}
