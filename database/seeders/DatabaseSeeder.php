<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AssemblySeeder::class,
            ChartAccountSeeder::class,
        ]);

        $admin = User::updateOrCreate(['email' => 'admin@church.local'], [
            'name' => 'System Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $admin->assemblies()->syncWithoutDetaching(\App\Models\Assembly::pluck('id'));
    }
}
