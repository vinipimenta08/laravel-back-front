<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;


class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (env('ENVIRONMENT') == 'DEV') {
            $faker = Faker::create();
            DB::connection('mysql2')->table('clients')->insert([
                'name' => 'usuario teste',
                'contact' => 'usuario_teste@gmail.com',
                'password' => bcrypt('usuario_teste@gmail.com'),
                'active' => 1,
            ]);

            $name = $faker->name();
            $contact = $faker->unique()->safeEmail();
            DB::connection('mysql2')->table('clients')->insert([
                'name' => $name,
                'contact' => $contact,
                'password' => bcrypt($contact),
                'active' => 1,
            ]);

            $name = $faker->name();
            $contact = $faker->unique()->safeEmail();
            DB::connection('mysql2')->table('clients')->insert([
                'name' => $name,
                'contact' => $contact,
                'password' => bcrypt($contact),
                'active' => 1,
            ]);
        }else {
            DB::connection('mysql2')->table('clients')->insert([
                'name' => 'Genion',
                'contact' => 'genion@geniontechnology.com.br',
                'password' => bcrypt('genion@geniontechnology.com.br'),
                'active' => 1,
            ]);
        }
    }
}
