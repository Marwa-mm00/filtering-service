<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Software Development', 'Marketing', 'Design', 'Sales'];
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }

        for ($i = 1; $i <= 10; $i++) {
            Job::find($i)->categories()->attach(rand(1, 4));
        }

    }
}
