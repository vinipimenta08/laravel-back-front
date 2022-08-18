<?php

namespace Database\Seeders;

use App\Models\StatusSendKolmeya;
use Illuminate\Database\Seeder;

class StatusSendKolmeyaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StatusSendKolmeya::create(
            [
                'status' => 'TENTANDO ENVIAR',
                'active' => 1
            ]
        );
        StatusSendKolmeya::create(
            [
                'status' => 'ENVIADO',
                'active' => 1
            ]
        );
        StatusSendKolmeya::create(
            [
                'status' => 'ENTREGUE',
                'active' => 1
            ]
        );
        StatusSendKolmeya::create(
            [
                'status' => 'NÃO ENTREGUE',
                'active' => 1
            ]
        );
        StatusSendKolmeya::create(
            [
                'status' => 'REJEITADO NA OPERADORA',
                'active' => 1
            ]
        );
    }
}
