<?php

namespace Database\Seeders;

use App\Models\LocationBarangay;
use App\Models\LocationCity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class PsgcLocationSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('location_cities') || ! Schema::hasTable('location_barangays')) {
            return;
        }

        $jsonPath = database_path('data/psgc_locations.json');
        if (! File::exists($jsonPath)) {
            return;
        }

        $dataset = json_decode(File::get($jsonPath), true);
        if (! is_array($dataset)) {
            return;
        }

        $hasCityPsgc = Schema::hasColumn('location_cities', 'psgc_code');
        $hasBrgyPsgc = Schema::hasColumn('location_barangays', 'psgc_code');

        foreach ($dataset['cities'] ?? [] as $cityData) {
            $cityAttributes = [
                'country' => $cityData['country'] ?? 'Philippines',
                'zip_code' => $cityData['zip_code'],
            ];

            if ($hasCityPsgc) {
                $cityAttributes['psgc_code'] = $cityData['psgc_code'] ?? null;
                $cityAttributes['region_code'] = $cityData['region_code'] ?? null;
                $cityAttributes['region_name'] = $cityData['region_name'] ?? null;
                $cityAttributes['province_code'] = $cityData['province_code'] ?? null;
                $cityAttributes['province_name'] = $cityData['province_name'] ?? null;
                $cityAttributes['city_class'] = $cityData['city_class'] ?? 'HUC';
            }

            $city = LocationCity::updateOrCreate(
                ['name' => $cityData['name']],
                $cityAttributes
            );

            foreach ($cityData['barangays'] ?? [] as $brgyData) {
                $brgyAttributes = [];
                if ($hasBrgyPsgc) {
                    $brgyAttributes['psgc_code'] = $brgyData['psgc_code'] ?? null;
                    $brgyAttributes['city_psgc_code'] = $cityData['psgc_code'] ?? null;
                    $brgyAttributes['urban_rural'] = $brgyData['urban_rural'] ?? 'U';
                }

                LocationBarangay::updateOrCreate(
                    [
                        'location_city_id' => $city->id,
                        'name' => $brgyData['name'],
                    ],
                    $brgyAttributes
                );
            }
        }
    }
}
