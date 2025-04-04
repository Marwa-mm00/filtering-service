<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['city' => 'New York', 'state' => 'NY', 'country' => 'USA'],
            ['city' => 'San Francisco', 'state' => 'CA', 'country' => 'USA'],
            ['city' => 'London', 'state' => 'N/A', 'country' => 'UK']
        ];
        foreach ($locations as $location) {
            Location::create($location);
        }

        for ($i = 1; $i <= 10; $i++) {
            Job::find($i)->locations()->attach(rand(1, 3));
        }

    }
}
