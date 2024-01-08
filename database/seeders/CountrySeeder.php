<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::truncate();
  
        $csvFile = fopen(base_path("database\data\countries.csv"), "r");
  
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                Country::create([
                    "name" => $data['2'],
                    "code" => $data['1']
                ]);    
            }
            $firstline = false;
        }
   
        fclose($csvFile);
    }
}
