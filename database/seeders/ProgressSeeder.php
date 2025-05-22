<?php

namespace Database\Seeders;

use App\Models\Progress;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $officer = User::where('role', 'officer')->first();

        if (!$officer) {
            $this->command->warn('No officer user found. Skipping ProgressSeeder.');
            return;
        }

        Progress::create([
            'user_id' => $officer->id,
            'project_id' => $officer->project_id,
            'date' => now()->toDateString(),
            'progress_percent' => 60,
            'evidence_path' => 'contoh.jpg',
            'description' => 'Pekerjaan struktur lantai 1 selesai',
            'status' => 'pending', // sesuai enum di migrasi
        ]);
    }
}
