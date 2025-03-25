<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = ['PHP', 'JavaScript', 'Python', 'Ruby', 'Go'];
        foreach ($languages as $lang) {
            Language::create(['name' => $lang]);
        }

    }
}
