<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //admin user
        User::create([
            'name' => 'Bao Hung',
            'email' => 'Steven.tran@tbht.vn',
            'password' => bcrypt('123456'),
            'role' => User::ROLES['admin'],
        ]);

        //user
        User::create([
            'name' => 'Huy Pho',
            'email' => 'phohuy@tbht.vn',
            'password' => bcrypt('123456'),
            'role' => User::ROLES['user'],
        ]);
    }
}
