<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListCustomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('ENVIRONMENT') == 'DEV') {
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
            DB::connection('mysql2')->table('list_custom')->insert([
                'id_client' => 1,
                'id_campaign' => 1,
                'ddd' => 11,
                'phone' => 912345678,
                'message_sms' => Str::random(50),
                'date_event' => now(),
                'title' => Str::random(10),
                'description' => Str::random(10),
                'location' => Str::random(100),
                'joker_one' => Str::random(100),
                'joker_two' => Str::random(100),
                'identification' => Str::random(100),
                'id_send_sms' => 3,
                'id_sms' => 0,
                'hash' => 'c1sf4d1g5bd4f1fd5',
                'created_at' => now()
            ]);
        }
    }
}
