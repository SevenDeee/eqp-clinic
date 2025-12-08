<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Van Mercadal',
            'email' => 'van@gmail.com',
            'password' => 'van',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Alvin Constantino',
            'email' => 'alvin@gmail.com',
            'password' => 'alvin',
            'role' => 'user',
        ]);

        Patient::factory(10)->create()->each(function ($patient) {
            Prescription::factory()
                ->for($patient)
                ->create([
                    'prescribed_by' => rand(1, 2),
                ]);
        });

        Category::factory()->create([
            'name' => 'Frame Type',
        ]);
        Category::factory()->create([
            'name' => 'Frame Color',
        ]);
        Category::factory()->create([
            'name' => 'Lens Supply',
        ]);
    }
}
