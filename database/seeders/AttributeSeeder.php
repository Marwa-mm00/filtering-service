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
            ['name' => 'Work Permit Required', 'type' => 'boolean'],
            ['name' => 'Shift Timing', 'type' => 'select', 'options' => json_encode(['Morning', 'Evening', 'Night'])],
            ['name' => 'Required Certifications', 'type' => 'text'],
            ['name' => 'Technical Assessment Required', 'type' => 'boolean'],
            ['name' => 'Work Environment', 'type' => 'select', 'options' => json_encode(['Office', 'Hybrid', 'Remote'])],
            ['name' => 'Equipment Provided', 'type' => 'boolean'],
            ['name' => 'Contract Duration', 'type' => 'number'],
            ['name' => 'Probation Period', 'type' => 'number'],
            ['name' => 'Visa Sponsorship', 'type' => 'boolean']
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
