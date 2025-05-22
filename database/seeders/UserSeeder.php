<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectA = Project::where('name', 'Proyek A')->first();
        $projectB = Project::where('name', 'Proyek B')->first();

        User::insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'pin' => '1234',
                'project_id' => null,
            ],
            [
                'name' => 'Officer A',
                'email' => 'officerA@example.com',
                'password' => Hash::make('password'),
                'role' => 'officer',
                'pin' => '1234',
                'project_id' => $projectA->id,
            ],
            [
                'name' => 'PM A',
                'email' => 'pmA@example.com',
                'password' => Hash::make('password'),
                'role' => 'pm',
                'pin' => '1234',
                'project_id' => $projectA->id,
            ],
            [
                'name' => 'VP QHSE',
                'email' => 'vp@example.com',
                'password' => Hash::make('password'),
                'role' => 'vpqHSE',
                'pin' => '1234',
                'project_id' => null,
            ],
        ]);
    }
}
