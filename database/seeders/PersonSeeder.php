<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        $people = [
            [
                'name' => 'Emma Watson',
                'age' => 28,
                'pictures' => [
                    'https://picsum.photos/400/500?random=1',
                    'https://picsum.photos/400/500?random=2',
                ],
                'location' => 'New York, NY',
            ],
            [
                'name' => 'John Smith',
                'age' => 32,
                'pictures' => [
                    'https://picsum.photos/400/500?random=3',
                    'https://picsum.photos/400/500?random=4',
                ],
                'location' => 'Los Angeles, CA',
            ],
            [
                'name' => 'Sarah Johnson',
                'age' => 25,
                'pictures' => [
                    'https://picsum.photos/400/500?random=5',
                ],
                'location' => 'Chicago, IL',
            ],
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
        
        for ($i = 0; $i < 50; $i++) {
            Person::create([
                'name' => fake()->name(),
                'age' => fake()->numberBetween(18, 60),
                'pictures' => [
                    'https://picsum.photos/400/500?random=' . (100 + $i),
                ],
                'location' => fake()->city() . ', ' . fake()->stateAbbr(),
            ]);
        }
    }
}
