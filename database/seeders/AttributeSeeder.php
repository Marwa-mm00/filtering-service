<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Job;
use App\Models\JobAttributeValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            ['name' => 'work_permit_required', 'type' => 'boolean'],
            ['name' => 'shift', 'type' => 'select', 'options' => json_encode(['Morning', 'Evening', 'Night'])],
            ['name' => 'certifications', 'type' => 'text'],
            ['name' => 'environment', 'type' => 'select', 'options' => json_encode(['Office', 'Hybrid', 'Remote'])],
            ['name' => 'contract_duration', 'type' => 'number'],
            ['name' => 'probation_period', 'type' => 'number'],
            ['name' => 'start_date', 'type' => 'date']
        ];
        foreach ($attributes as $attribute) {
            Attribute::create($attribute);
        }

        foreach (Job::all() as $job) {
            foreach (Attribute::all() as $attribute) {
                JobAttributeValue::create([
                    'job_id' => $job->id,
                    'attribute_id' => $attribute->id,
                    'value' => match ($attribute->type) {
                        'number' => rand(1, 12),
                        'boolean' => (bool)rand(0, 1),
                        'date' => now()->addDays(rand(1, 365)),
                        'select' => isset($attribute->options) ? json_decode($attribute->options)[array_rand(json_decode($attribute->options))] : null,
                        'text' => Str::random(50),
                    },
                ]);
            }
        }
    }
}
