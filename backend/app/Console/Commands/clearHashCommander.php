<?php

namespace App\Console\Commands;

use App\Models\ListCustom;
use App\Models\ListHash;
use Illuminate\Console\Command;

class clearHashCommander extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:clearhash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clears the hash of events that have already passed';

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
        $listCustom = ListHash::join('list_custom', 'list_custom.id', 'list_hash.id_list_custom')
                                ->where('list_custom.date_event', '<', date('Y-m-d'))
                                ->select('list_hash.id')   
                                ->limit(1000) 
                                ->get()
                                ->toArray();
        ListHash::whereIn('id', $listCustom)->delete();
        return 0;
    }
}
