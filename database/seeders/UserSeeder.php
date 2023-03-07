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
            'code' => 'abc123',
            'sex' => 'male',
            'dob' => '1999-01-01',
            'doj' => '2021-01-01',
            'phone' => '0123456789',
            'address' => 'Hanoi',
        ]);

        //user
        User::create([
            'name' => 'Huy Pho',
            'email' => 'phohuy@tbht.vn',
            'password' => bcrypt('123456'),
            'role' => User::ROLES['user'],
            'code' => 'abc456',
            'sex' => 'male',
            'dob' => '1999-01-01',
            'doj' => '2021-01-01',
            'phone' => '0123456789',
            'address' => 'Hanoi',
        ]);
    }
}
