<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to the JSON file
        $json = File::get(public_path('response1762511864585.json'));
        $provinces = json_decode($json, true);

        // Prepare the data for batch insert
        $data = array_map(function ($province) {
            return [
                'activity' => $province['activity'],
                'flag_active' => $province['flag_active'],
                'istat_code_province' => $province['istat_code_province'],
                // Ensure province_code is at most 2 characters long
                'province_code' => substr($province['province_code'], 0, 2),
                'province_description' => $province['province_description'],
                'region_code' => $province['region_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $provinces);

        // Insert or update data to handle duplicates
        foreach ($data as $province) {
            // First, try to update by istat_code_province (primary key)
            $updated = DB::table('provincie')
                ->where('istat_code_province', $province['istat_code_province'])
                ->update($province);
            
            // If no rows were updated, try to insert
            if ($updated === 0) {
                try {
                    DB::table('provincie')->insert($province);
                } catch (\Illuminate\Database\QueryException $e) {
                    // If insert fails due to duplicate province_code, skip this record
                    if (str_contains($e->getMessage(), 'provincie_province_code_unique')) {
                        continue;
                    }
                    throw $e;
                }
            }
        }
    }
}
