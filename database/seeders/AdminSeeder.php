<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Admin::exists()) {
        Admin::create([
            'name' => 'Super Admin',
            'phone' => '+201271491240',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
        ]);
    }
    }
}
