<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            "is_admin" => true,
            "username" => "admin",
            "password" => Hash::make("admin"),
            "fullname" => "Admin",
            "gender" => "MALE"
        ]);

        User::create([
            "is_admin" => false,
            "username" => "farandi",
            "password" => Hash::make("angesti"),
            "fullname" => "Farandi Angesti",
            "gender" => "MALE"
        ]);
    }
}
