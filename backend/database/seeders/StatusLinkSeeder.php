<?php

namespace Database\Seeders;

use App\Models\StatusLink;
use Illuminate\Database\Seeder;

class StatusLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StatusLink::create([
            'status' => 'SMS ENVIADO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'SMS ERRO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'LINK',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'PROCESSANDO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'TRATANDO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'ENVIANDO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'ENTREGUE',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'SEM SALDO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'NUMERO INVALIDO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'NUMERO BLOQUEADO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'EM FILA',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'PAUSADO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'LISTA NEGRA OPERADORA',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'MENSAGEM MAL FORMATADA',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'ERRO',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'BLACKLIST',
            'active' => 1
        ]);

        StatusLink::create([
            'status' => 'AGENDADO',
            'active' => 1
        ]);
    }
}
