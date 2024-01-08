<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::truncate();
  
        $csvFile = fopen(base_path("database\data\cities.csv"), "r");
  
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                City::create([
                    "name" => $data[1],
                    "state_id" => $data[2]
                ]);    
            }
            $firstline = false;
        }
   
        fclose($csvFile);
    }
}
