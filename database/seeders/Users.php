<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // add admin
        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@presensiku.com',
            'password' => 'admin123',
            'role' => 1,
            'image' => 'https://ui-avatars.com/api/?name=admin&color=7F9CF5&background=EBF4FF',
        ]);
    }
}
