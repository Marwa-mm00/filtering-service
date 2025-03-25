<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 1; $i <= 10; $i++) {
             Job::create([
                'title' => "Job Title $i",
                'description' => "Description for job $i",
                'company_name' => "Company $i",
                'salary_min' => rand(30000, 50000),
                'salary_max' => rand(60000, 100000),
                'is_remote' => (bool)rand(0, 1),
                'job_type' => ['full-time', 'part-time', 'contract', 'freelance'][array_rand(['full-time', 'part-time', 'contract', 'freelance'])],
                'status' => ['draft', 'published', 'archived'][array_rand(['draft', 'published', 'archived'])],
                'published_at' => now(),
            ]);
        }

    }
}
