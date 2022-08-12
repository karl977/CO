<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SyncSchedule;
use App\Models\Leg;
use App\Models\PriceList;
use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Feature\Data\ScheduleData;
use Tests\TestCase;

class SyncScheduleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test schedule data parsing
     *
     * @return void
     */
    public function test_data_parsing()
    {
        $data = ScheduleData::get();

        $controller = new SyncSchedule();
        $controller->parseFromRequestBodyToDb($data);

        $pricelistId = $data['id'];
        $pricelist = PriceList::where('id', $pricelistId)->first();

        // Test pricelist
        $this->assertNotNull($pricelist);
        $this->assertEquals(Carbon::parse($data['validUntil'])->format("Y-m-d H:i:s"), $pricelist->valid_until->format("Y-m-d H:i:s"));

        // Test legs
        $this->assertEquals(3, count(Leg::all()));
        $leg = Leg::where("id", $data['legs'][0]['routeInfo']["id"])->first();
        $this->assertNotNull($leg);
        $this->assertEquals($data['legs'][0]['routeInfo']["from"]["id"], $leg->from_planet_id);
        $this->assertEquals($data['legs'][0]['routeInfo']["distance"], $leg->distance);

        // Test providers
        $provider = Provider::where("id", $data['legs'][0]['providers'][0]["id"])->first();
        $this->assertNotNull($provider);
        $company = $provider->company()->first();
        $this->assertNotNull($company);
        $this->assertEquals($data['legs'][0]['providers'][0]['company']["id"], $company->id);
        $this->assertEquals($data['legs'][0]['providers'][0]['price'], $provider->price);
        $this->assertEquals(Carbon::parse($data['legs'][0]['providers'][0]['flightStart'])->format("Y-m-d H:i:s"), $provider->flight_start->format("Y-m-d H:i:s"));
        $this->assertEquals(Carbon::parse($data['legs'][0]['providers'][0]['flightEnd'])->format("Y-m-d H:i:s"), $provider->flight_end->format("Y-m-d H:i:s"));
    }
}
