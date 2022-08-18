<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListHashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('ENVIRONMENT') == 'DEV') {
            DB::connection('mysql2')->table('list_hash')->insert([
                'id_list_custom' => 1,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'id_status' => 1,
                'created_at' => now(),
            ]);
        }
    }
}
