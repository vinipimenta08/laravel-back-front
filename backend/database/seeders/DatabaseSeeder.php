<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            ProfilesSeeder::class,
            ClientsSeeder::class,
            UsersAndNotesSeeder::class,
            MenusTableSeeder::class,
            StatusLinkSeeder::class,
            StatusLinkSended::class,
            // CampaignsSeeder::class,
            // ListCustomSeeder::class,
            // ListHashSeeder::class,
            ValueFireSeeder::class,
            StatusSendKolmeyaSeeder::class
        ]);
    }
}
