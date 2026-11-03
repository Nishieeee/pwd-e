<?php

namespace Database\Seeders;

use App\Models\ExclusionaryTerm;
use Illuminate\Database\Seeder;

class ExclusionaryTermSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            ['phrase' => 'able-bodied', 'category' => 'ability', 'suggestion' => 'qualified applicant'],
            ['phrase' => 'normal person', 'category' => 'ability', 'suggestion' => 'person without disability'],
            ['phrase' => 'handicapped', 'category' => 'disability', 'suggestion' => 'person with disability'],
            ['phrase' => 'suffers from', 'category' => 'disability', 'suggestion' => 'has / lives with'],
            ['phrase' => 'wheelchair-bound', 'category' => 'disability', 'suggestion' => 'wheelchair user'],
        ];

        foreach ($terms as $term) {
            ExclusionaryTerm::firstOrCreate($term + ['is_active' => true]);
        }
    }
}
