<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\TripsController;
use App\Jobs\SyncSchedule;
use App\Models\Company;
use App\Models\Planet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Feature\Data\ScheduleData;
use Tests\TestCase;

class TripsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test providers of found trips
     *
     * @return void
     */
    public function test_providers(): void
    {
        $data = ScheduleData::get();

        $controller = new SyncSchedule();
        $controller->parseFromRequestBodyToDb($data);

        $controller = new TripsController();

        $fromPlanet = Planet::where("name", "Jupiter")->first();
        $toPlanet = Planet::where("name", "Venus")->first();

        $this->assertNotNull($fromPlanet);
        $this->assertNotNull($toPlanet);

        $trips = $controller->findTrips($fromPlanet, $toPlanet, [], "price", 100);
        $this->assertEquals(6, count($trips));


        // Test trip data
        $trip = $trips[0];
        $this->assertEquals($data["legs"][2]["routeInfo"]["distance"], $trip->distance);
        $this->assertEquals($data["legs"][2]["providers"][1]["price"], $trip->price);
        $duration = Carbon::parse($data["legs"][2]["providers"][1]["flightStart"])->diffInSeconds(Carbon::parse($data["legs"][2]["providers"][1]["flightEnd"]));
        $this->assertEquals($duration, $trip->duration);
        $this->assertEquals(Carbon::parse($data["legs"][2]["providers"][1]["flightStart"])->format("Y-m-d H:i:s"), $trip->start->format("Y-m-d H:i:s"));
        $this->assertEquals(Carbon::parse($data["legs"][2]["providers"][1]["flightEnd"])->format("Y-m-d H:i:s"), $trip->end->format("Y-m-d H:i:s"));

        // Test provider data
        $this->assertEquals($data["legs"][2]["providers"][1]["id"], $trip->providers[0]["id"]);
        $this->assertEquals(Carbon::parse($data["legs"][2]["providers"][1]["flightStart"])->format("Y-m-d H:i:s"), Carbon::parse($trip->providers[0]["flight_start"])->format("Y-m-d H:i:s"));
        $this->assertEquals(Carbon::parse($data["legs"][2]["providers"][1]["flightEnd"])->format("Y-m-d H:i:s"), Carbon::parse($trip->providers[0]["flight_end"])->format("Y-m-d H:i:s"));
        $this->assertEquals($duration, $trip->providers[0]["duration"]);
        $this->assertEquals($data["legs"][2]["providers"][1]["company"]["name"], $trip->providers[0]["company_name"]);
        $this->assertEquals($data["legs"][2]["routeInfo"]["from"]["name"], $trip->providers[0]["from_planet_name"]);
        $this->assertEquals($data["legs"][2]["routeInfo"]["to"]["name"], $trip->providers[0]["to_planet_name"]);

        // Test another trip data
        $trip = $trips[5];
        $provider1 = $data["legs"][0]["providers"][2];
        $provider2 = $data["legs"][1]["providers"][1];
        $distance = $data["legs"][0]["routeInfo"]["distance"] + $data["legs"][1]["routeInfo"]["distance"];
        $this->assertEquals($distance, $trip->distance);

        $price = $provider1["price"] + $provider2["price"];
        $this->assertEquals($price, $trip->price);

        $duration = 0;
        $duration += Carbon::parse($provider1["flightStart"])->diffInSeconds(Carbon::parse($provider1["flightEnd"]));
        $duration += Carbon::parse($provider1["flightEnd"])->diffInSeconds(Carbon::parse($provider2["flightStart"]));
        $duration += Carbon::parse($provider2["flightStart"])->diffInSeconds(Carbon::parse($provider2["flightEnd"]));
        $this->assertEquals($duration, $trip->duration);

        $this->assertEquals(Carbon::parse($provider1["flightStart"])->format("Y-m-d H:i:s"), $trip->start->format("Y-m-d H:i:s"));
        $this->assertEquals(Carbon::parse($provider2["flightEnd"])->format("Y-m-d H:i:s"), $trip->end->format("Y-m-d H:i:s"));

        $this->assertEquals($provider1["id"], $trip->providers[0]["id"]);
        $this->assertEquals($provider2["id"], $trip->providers[1]["id"]);
    }

    /**
     * Test sorting of trips controller
     *
     * @return void
     */
    public function test_sort(): void
    {

        $data = ScheduleData::get();

        $controller = new SyncSchedule();
        $controller->parseFromRequestBodyToDb($data);

        $controller = new TripsController();

        $fromPlanet = Planet::where("name", "Jupiter")->first();
        $toPlanet = Planet::where("name", "Venus")->first();

        $this->assertNotNull($fromPlanet);
        $this->assertNotNull($toPlanet);

        $trips = $controller->findTrips($fromPlanet, $toPlanet, [], "price", 100);
        $price = 0;
        foreach ($trips as $trip) {
            $this->assertLessThanOrEqual($trip->price, $price);
            $price = $trip->price;
        }

        $trips = $controller->findTrips($fromPlanet, $toPlanet, [], "distance", 100);
        $distance = 0;
        foreach ($trips as $trip) {
            $this->assertLessThanOrEqual($trip->distance, $distance);
            $distance = $trip->distance;
        }

        $trips = $controller->findTrips($fromPlanet, $toPlanet, [], "duration", 100);
        $duration = 0;
        foreach ($trips as $trip) {
            $this->assertLessThanOrEqual($trip->duration, $duration);
            $duration = $trip->duration;
        }
    }

    /**
     * Test company filter of trips controller
     *
     * @return void
     */
    public function test_company_filter(): void
    {
        $data = ScheduleData::get();

        $controller = new SyncSchedule();
        $controller->parseFromRequestBodyToDb($data);

        $controller = new TripsController();

        $fromPlanet = Planet::where("name", "Jupiter")->first();
        $toPlanet = Planet::where("name", "Venus")->first();

        $this->assertNotNull($fromPlanet);
        $this->assertNotNull($toPlanet);

        $company1 = Company::where("name", "Space Piper")->first();
        $this->assertNotNull($company1);

        $trips = $controller->findTrips($fromPlanet, $toPlanet, [$company1], "distance", 100);
        $this->assertEquals(2, count($trips));
        foreach ($trips as $trip) {
            foreach ($trip->providers as $provider) {
                $this->assertEquals("Space Piper", $provider["company_name"]);
            }
        }

        $company2 = Company::where("name", "Explore Dynamite")->first();
        $trips = $controller->findTrips($fromPlanet, $toPlanet, [$company1, $company2], "distance", 100);
        $this->assertEquals(3, count($trips));
        foreach ($trips as $trip) {
            foreach ($trip->providers as $provider) {
                $this->assertContains($provider["company_name"], ["Space Piper", "Explore Dynamite"]);
            }
        }
    }

    /**
     * Test limit of trips controller
     *
     * @return void
     */
    public function test_limit(): void
    {
        $data = ScheduleData::get();

        $controller = new SyncSchedule();
        $controller->parseFromRequestBodyToDb($data);

        $controller = new TripsController();

        $fromPlanet = Planet::where("name", "Jupiter")->first();
        $toPlanet = Planet::where("name", "Venus")->first();

        $this->assertNotNull($fromPlanet);
        $this->assertNotNull($toPlanet);

        $trips = $controller->findTrips($fromPlanet, $toPlanet, [], "distance", 3);
        $this->assertEquals(3, count($trips));
    }
}
