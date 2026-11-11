<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Wheelchair-accessible entrance', 'category' => 'mobility'],
            ['name' => 'Ramp access', 'category' => 'mobility'],
            ['name' => 'Accessible restroom', 'category' => 'mobility'],
            ['name' => 'Screen reader compatible', 'category' => 'visual'],
            ['name' => 'High-contrast display', 'category' => 'visual'],
            ['name' => 'Large text / zoom', 'category' => 'visual'],
            ['name' => 'Sign language (FSL) interpreter', 'category' => 'hearing'],
            ['name' => 'Captioned meetings', 'category' => 'hearing'],
            ['name' => 'Quiet workspace', 'category' => 'speech'],
            ['name' => 'Flexible schedule', 'category' => 'flexible_work'],
            ['name' => 'Remote work option', 'category' => 'flexible_work'],
            ['name' => 'Assistive technology provided', 'category' => 'cognitive'],
        ];

        foreach ($items as $item) {
            Accommodation::firstOrCreate($item);
        }
    }
}
