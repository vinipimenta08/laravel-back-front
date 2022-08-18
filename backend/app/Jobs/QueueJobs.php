<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $hashQueue = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($hashQueue)
    {
        $this->hashQueue = $hashQueue;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        shell_exec("php ".base_path()."/artisan queue:work --queue $this->hashQueue --once --timeout=1800 > /dev/null &");
    }
}