<?php

namespace Database\Seeders;

use App\Models\Status_link;
use Illuminate\Database\Seeder;

class StatusLinkSended extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status_link::create([
            'name' => 'SEM INTERAÇÃO'
        ]);

        Status_link::create([
            'name' => 'VISUALIZADO'
        ]);
    }
}
