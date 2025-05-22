<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::insert([
            ['name' => 'Proyek A'],
            ['name' => 'Proyek B'],
        ]);
    }
}
