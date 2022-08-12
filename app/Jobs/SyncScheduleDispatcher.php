<?php

namespace App\Jobs;

use App\Models\PriceList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SyncScheduleDispatcher implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get latest pricelist
        $latestPricelist = PriceList::orderBy('valid_until', 'desc')->first();

        // No pricelist found, try to get one immediately
        if(is_null($latestPricelist)){
            SyncSchedule::dispatch();
            return;
        }

        // Check validity of latest pricelist
        $validUntil = Carbon::parse($latestPricelist->valid_until);

        if($validUntil->isBefore(Carbon::now())){
            // Pricelist expired, try getting new one
            SyncSchedule::dispatch();
        }else{
            // Pricelist expires in future, schedule sync job 1 second after expiry
            $now = Carbon::now();
            SyncSchedule::dispatch()->delay(now()->addSeconds($now->diffInSeconds($validUntil) + 1));
        }
    }
}
