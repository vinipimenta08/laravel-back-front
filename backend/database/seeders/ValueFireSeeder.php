<?php

namespace Database\Seeders;

use App\Models\ValueFire;
use Illuminate\Database\Seeder;

class ValueFireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ValueFire::create(
            [
                'qtd_min' => 1,
                'qtd_max' => 50000,
                'value' => 0.25,
            ]
        );

        ValueFire::create(
            [
                'qtd_min' => 50001,
                'qtd_max' => 100000,
                'value' => 0.20,
            ]
        );

        ValueFire::create(
            [
                'qtd_min' => 100001,
                'qtd_max' => 200000,
                'value' => 0.18,
            ]
        );
        ValueFire::create(
            [
                'qtd_min' => 200001,
                'qtd_max' => 1000000,
                'value' => 0.15,
            ]
        );

    }
}
