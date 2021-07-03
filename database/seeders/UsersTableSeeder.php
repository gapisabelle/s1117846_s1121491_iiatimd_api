<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'Isabelle Oosterbaan',
            'email' => 'isa@test.nl',
            'password' => bcrypt('laravel'),
        ]);
        DB::table('users')->insert([
            'username' => 'Roy Oosterlee',
            'email' => 'roy@test.nl',
            'password' => bcrypt('laravel'),
        ]);
    }
}
