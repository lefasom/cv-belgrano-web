<?php

namespace Database\Seeders;

use App\Models\Cv;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Skill;
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

        $user = User::factory()->create([
            'name' => 'Usuario de prueba',
            'email' => 'cvbelgranosoporte@gmail.com',
        ]);

        $cvs = $user->cvs()->createMany(
            Cv::factory()->count(2)->make()->toArray()
        );

        $cvs->each(function (Cv $cv) {
            $cv->education()->createMany(
                Education::factory()->count(5)->make()->toArray()
            );
            $cv->experiences()->createMany(
                Experience::factory()->count(5)->make()->toArray()
            );
            $cv->skills()->createMany(
                Skill::factory()->count(5)->make()->toArray()
            );
        });

    }
}
