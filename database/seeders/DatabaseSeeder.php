<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@app.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        // Petugas
        User::create([
            'name' => 'Petugas Lab',
            'email' => 'petugas@app.com',
            'password' => bcrypt('password'),
            'role' => 'petugas'
        ]);
        // Peminjam
        User::create([
            'name' => 'Siswa 1',
            'email' => 'siswa@app.com',
            'password' => bcrypt('password'),
            'role' => 'peminjam'
        ]);
    }
}
