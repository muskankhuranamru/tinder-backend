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
        
        $names = ['Alex', 'Blake', 'Casey', 'Drew', 'Elliot', 'Finley', 'Gray', 'Harper', 'Indigo', 'Jordan'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'Austin'];
        $states = ['NY', 'CA', 'IL', 'TX', 'AZ', 'PA', 'FL', 'OH', 'GA', 'NC'];
        
        for ($i = 0; $i < 50; $i++) {
            Person::create([
                'name' => $names[$i % 10] . ' ' . $lastNames[($i + 5) % 10],
                'age' => 18 + ($i % 43),
                'pictures' => [
                    'https://picsum.photos/400/500?random=' . (100 + $i),
                ],
                'location' => $cities[$i % 10] . ', ' . $states[$i % 10],
            ]);
        }
    }
}
