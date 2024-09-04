<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //create dummy data
        for ($i=0; $i <= 10; $i++) {
            # code...
            User::create([
                'bp_code' => 'APFEY',
                'name' => Str::random(10),
                'role' => (string)rand(1, 4),
                'status' => '1',
                'username' => Str::random(10),
                'password' => Hash::make('12345678'),
                'email' => Str::random(10).'@example.com',
            ]);
        }
    }
}
