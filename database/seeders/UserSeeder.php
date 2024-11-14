<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password123'),
                'age' => 30,
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password123'),
                'age' => 25,
                'email_verified_at' => Carbon::now(),
                'remember_token' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        DB::table('password_reset_tokens')->insert([
            [
                'email' => 'john.doe@example.com',
                'token' => Str::random(60),
                'created_at' => Carbon::now(),
            ],
            [
                'email' => 'jane.smith@example.com',
                'token' => Str::random(60),
                'created_at' => Carbon::now(),
            ],
        ]);

        DB::table('sessions')->insert([
            [
                'id' => Str::uuid()->toString(),
                'user_id' => 1,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                'payload' => serialize(['some' => 'data']),
                'last_activity' => Carbon::now()->timestamp,
            ],
            [
                'id' => Str::uuid()->toString(),
                'user_id' => 2,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                'payload' => serialize(['some' => 'data']),
                'last_activity' => Carbon::now()->timestamp,
            ],
        ]);
    }
}
