<?php

namespace Database\Seeders;

use App\Models\Assembly;
use Illuminate\Database\Seeder;

class AssemblySeeder extends Seeder
{
    public function run(): void
    {
        Assembly::updateOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Eastview Assembly',
                'location' => 'Main branch',
                'status' => 'active',
            ]
        );
    }
}
