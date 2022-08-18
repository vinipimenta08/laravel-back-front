<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class databaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily database backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $database = [
            env('DB_DATABASE') => [
                'DB_USERNAME'   => env('DB_USERNAME'),
                'DB_PASSWORD'   => env('DB_PASSWORD'),
                'DB_HOST'       => env('DB_HOST')
            ],
            env('DB_DATABASE_SECOND') => [
                'DB_USERNAME'   => env('DB_USERNAME_SECOND'),
                'DB_PASSWORD'   => env('DB_PASSWORD_SECOND'),
                'DB_HOST'       => env('DB_HOST_SECOND')
            ]
        ];

        foreach ($database as $db => $value) {
            $storagePath = storage_path() . "/app/backup/".$db;

            if(!is_dir(storage_path() . "/app/backup/")){
                mkdir(storage_path() . "/app/backup/", 0777, true);
            }
            if(!is_dir($storagePath)){
                mkdir($storagePath, 0777, true);
            }

            $files = array_diff(scandir($storagePath), array(".",".."));
            if(count($files) > 3){
                unlink($storagePath . '/' . current($files));
            }

            $filename = Carbon::now()->format('Ymd') . ".gz";
            $command = "mysqldump --user=" . $value['DB_USERNAME'] ." --password=" . $value['DB_PASSWORD'] . " --host=" . $value['DB_HOST'] . " " .  $db . "  | gzip > " . $storagePath . '/' . $filename;
            $returnVar = NULL;
            $output  = NULL;
            exec($command, $output, $returnVar);
        }
    }
}
