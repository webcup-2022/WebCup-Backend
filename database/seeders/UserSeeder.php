<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        User::create([
            "name" => "mgcodeur",
            "email" => "mgcodeur@gmail.com",
            "password" => Hash::make('password')
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
