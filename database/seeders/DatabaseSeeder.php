<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@jb2027.com',
            'password' => bcrypt('admin123'),
        ]);

        $this->call([
            CandidateInformationSeeder::class,
            AgendaEventSeeder::class,
            SeccionesSeeder::class,
        ]);
    }
}
