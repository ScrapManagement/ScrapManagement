<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

      $users = [
            [
                'name' => 'Yousef Kelany',
                'email' => 'yousef@gmail.com',
                'password' => Hash::make('yousef12345'),
                'phone' => '+201271491240',

            ],

        ];

        foreach ($users as $user) {
            Admin::create($user);
        }

    }
}
