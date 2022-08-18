<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('ENVIRONMENT') == 'DEV') {
            DB::connection('mysql2')->table('campaigns')->insert([
                'id_client' => 1,
                'name' => 'campanha 1 do cliente 1',
                'active' => 1,
                'created_at' => now()
            ]);
    
            DB::connection('mysql2')->table('campaigns')->insert([
                'id_client' => 1,
                'name' => 'campanha 2 do cliente 1',
                'active' => 1,
                'created_at' => now()
            ]);
    
            DB::connection('mysql2')->table('campaigns')->insert([
                'id_client' => 1,
                'name' => 'campanha 3 do cliente 1',
                'active' => 1,
                'created_at' => now()
            ]);
    
            DB::connection('mysql2')->table('campaigns')->insert([
                'id_client' => 2,
                'name' => 'campanha 1 do cliente 2',
                'active' => 1,
                'created_at' => now()
            ]);
    
            DB::connection('mysql2')->table('campaigns')->insert([
                'id_client' => 2,
                'name' => 'campanha 2 do cliente 2',
                'active' => 1,
                'created_at' => now()
            ]);
    
            DB::connection('mysql2')->table('campaigns')->insert([
                'id_client' => 2,
                'name' => 'campanha 3 do cliente 2',
                'active' => 1,
                'created_at' => now()
            ]);
        }
    }
}
