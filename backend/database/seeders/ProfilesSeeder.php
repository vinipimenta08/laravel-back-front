<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('profiles')->insert([
            'name' => 'root',
            'active' => 1,
            'created_at' => now(),
        ]);

        DB::table('profiles')->insert([
            'name' => 'admin',
            'active' => 1,
            'created_at' => now(),
        ]);

        DB::table('profiles')->insert([
            'name' => 'user',
            'active' => 1,
            'created_at' => now(),
        ]);
    }
}
