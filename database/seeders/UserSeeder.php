<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['rut' => '16808624-5', 'password' => Hash::make('maxy2001'), 'name' => 'mauricio', 'apellido_p' => 'Morales', 'email' => 'mauriciomorales0410@gmail.com']);
    }

}
