<?php

namespace App\Jobs;

use App\BillingImport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessBillingGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group, $key, $last;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($key, $group, $last)
    {
        $this->key = $key;
        $this->group = $group;
        $this->last = $last;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        BillingImport::processBillingGroup($this->key, $this->group);

        if($this->key == $this->last) {
            BillingImport::query()->update(["status" => BillingImport::STATUS_COMPLETE]);
        }
    }
}
