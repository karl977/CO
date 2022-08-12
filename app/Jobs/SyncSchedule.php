<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Leg;
use App\Models\Planet;
use App\Models\PriceList;
use App\Models\Provider;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class SyncSchedule implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $apiUrl = env('PLANETS_SYNC_URL', 'https://cosmos-odyssey.azurewebsites.net/api/v1.0/TravelPrices');
        $response = Http::get($apiUrl);

        if (!$response->successful()) {
            // If request failed, try again in 30 seconds
            SyncScheduleDispatcher::dispatch()->delay(now()->addSeconds(30));
            return;
        }

        $data = $response->json();

        if(is_null($data)){
            SyncScheduleDispatcher::dispatch()->delay(now()->addSeconds(30));
            return;
        }

        $dispatchDelay = $this->parseFromRequestBodyToDb($data);
        if(!is_null($dispatchDelay)){
            SyncScheduleDispatcher::dispatch()->delay(now()->addSeconds($dispatchDelay));
            return;
        }

        // Keep 15 most recent pricelists
        $this->deleteOldPriceLists();

        // Sync again after current pricelist expires
        SyncScheduleDispatcher::dispatch();
    }

    /**
     * Parse request body to database
     *
     * @param array $data
     * @return int|null
     */
    public function parseFromRequestBodyToDb(array $data): ?int
    {

        $priceListId = $data["id"];
        $priceList = PriceList::where('id', $priceListId)->first();

        if($priceList == null){
            PriceList::create([
                'id' => $priceListId,
                'valid_until' => Carbon::parse($data["validUntil"])->format("Y-m-d H:i:s")
            ]);
        }else{
            $validUntil = Carbon::parse($data["validUntil"]);
            $now = Carbon::now();
            if($validUntil->isBefore($now)){
                // If current pricelist has expired, try again in 30 seconds
                return 30;
            }else{
                // If pricelist still valid, let SyncSheduleDispatcher schedule a job after expiry
                return 0;
            }
        }

        // We'll do manual mapping of planets -> id, since same planets of different legs have different ids
        $planetIds = [];

        foreach ($data['legs'] as $leg) {

            $fromPlanetName = $leg["routeInfo"]["from"]["name"];
            $fromPlanetId = $leg["routeInfo"]["from"]["id"];

            if(array_key_exists($fromPlanetName, $planetIds)){
                $fromPlanetId = $planetIds[$fromPlanetName];
            }else{
                Planet::firstOrCreate(
                    [
                        "id" => $fromPlanetId
                    ],
                    [
                        "price_list_id" => $priceListId,
                        "name" => $fromPlanetName
                    ]
                );
                $planetIds[$fromPlanetName] = $fromPlanetId;
            }

            $toPlanetName = $leg["routeInfo"]["to"]["name"];
            $toPlanetId = $leg["routeInfo"]["to"]["id"];

            if(array_key_exists($toPlanetName, $planetIds)){
                $toPlanetId = $planetIds[$toPlanetName];
            }else{
                Planet::firstOrCreate(
                    [
                        "id" => $toPlanetId
                    ],
                    [
                        "price_list_id" => $priceListId,
                        "name" => $toPlanetName
                    ]
                );
                $planetIds[$toPlanetName] = $toPlanetId;
            }

            Leg::firstOrCreate(
                [
                    "id" => $leg["routeInfo"]["id"]
                ],
                [
                    "price_list_id" => $priceListId,
                    "from_planet_id" => $fromPlanetId,
                    "to_planet_id" => $toPlanetId,
                    "distance" => $leg["routeInfo"]["distance"]
                ]
            );

            foreach($leg['providers'] as $provider){
                Company::firstOrCreate(
                    [
                        "id" => $provider["company"]["id"]
                    ],
                    [
                        "price_list_id" => $priceListId,
                        "name" => $provider["company"]["name"]
                    ]
                );

                Provider::firstOrCreate(
                    [
                        "id" => $provider["id"]
                    ],
                    [
                        "price_list_id" => $priceListId,
                        "leg_id" => $leg["routeInfo"]["id"],
                        "company_id" => $provider["company"]["id"],
                        "price" => $provider["price"],
                        "flight_start" => Carbon::parse($provider["flightStart"])->format("Y-m-d H:i:s"),
                        'flight_end' => Carbon::parse($provider["flightEnd"])->format("Y-m-d H:i:s")
                    ]
                );
            }
        }

        return null;
    }

    /**
     * Delete old pricelists
     *
     * @return void
     */
    public function deleteOldPriceLists(){
        $priceLists = PriceList::orderBy('valid_until', 'desc')->get();

        // Delete every pricelist besides 15 most recent ones
        for($i = count($priceLists); $i > 15 ; $i--){
            $priceLists[$i-1]->delete();
        }
    }
}
