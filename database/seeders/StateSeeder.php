<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;
class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::truncate();
  
        $csvFile = fopen(base_path("database\data\states.csv"), "r");
  
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                State::create([
                    "name" => $data[1],
                    "country_id" => $data[2]
                ]);    
            }
            $firstline = false;
        }
   
        fclose($csvFile);
    }
}
