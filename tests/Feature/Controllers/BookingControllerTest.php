<?php

namespace Tests\Feature\Controllers;

use App\Jobs\SyncSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\Feature\Data\ScheduleData;
use Tests\TestCase;
use Throwable;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test welcome page
     *
     * @return void
     */
    public function test_welcome_page()
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(
                fn(AssertableInertia $page) => $page
                    ->component("Welcome")
                    ->has("errors")
                    ->where("errors", [])
            );
    }

    /**
     * Test booking page
     *
     * @return void
     */
    public function test_booking_page()
    {
        $data = ScheduleData::get();

        $controller = new SyncSchedule();
        $controller->parseFromRequestBodyToDb($data);

        $this->get("/booking")
            ->assertOk()
            ->assertInertia(
                fn(AssertableInertia $page) => $page
                    ->component("Booking")
                    ->has("planets", 3)
                    ->has("companies", 5)
            );

        $this->get("/booking/Jupiter/Venus?sort=distance&companies=Spacelux")
            ->assertOk()
            ->assertInertia(
                fn(AssertableInertia $page) => $page
                    ->component("Booking")
                    ->has("planets", 3)
                    ->has("companies", 5)
                    ->has("search")
                    ->where("search.fromPlanetName", "Jupiter")
                    ->where("search.toPlanetName", "Venus")
                    ->where("search.sort", "distance")
                    ->where("search.companies", "Spacelux")
            );
    }

    /**
     * Test booking flow (search trips, select trip, confirm booking)
     *
     * @return void
     * @throws Throwable
     */
    public function test_booking_flow()
    {
        $data = ScheduleData::get();

        $controller = new SyncSchedule();
        $controller->parseFromRequestBodyToDb($data);

        // Search trips
        $props = [
            "fromPlanetName" => "Jupiter",
            "toPlanetName" => "Venus"
        ];
        $response = $this->call('GET', '/api/search-trips', $props);
        $this->assertEquals(200, $response->getStatusCode());
        $response->assertJsonCount(6, null);

        $responseJson = $response->decodeResponseJson();

        $trip = $responseJson[0];

        // Test select trip
        $response = $this->postJson('/api/select-trip', $trip);
        $this->assertEquals(200, $response->getStatusCode());

        $responseJson = $response->decodeResponseJson();
        $this->assertArrayHasKey("id", $responseJson);

        $bookingId = $responseJson["id"];
        $this->get("/booking-confirm/" . $bookingId)
            ->assertOk()
            ->assertInertia(
                fn(AssertableInertia $page) => $page
                    ->component("BookingConfirm")
                    ->has("trip")
                    ->where("trip_id", $bookingId)
            );

        // Test confirm booking
        $body = [
            "first_name" => "Test",
            "last_name" => "User"
        ];
        $response = $this->postJson('/api/booking-confirm/' . $bookingId, $body);
        $this->assertEquals(200, $response->getStatusCode());

        $responseJson = $response->decodeResponseJson();
        $this->assertArrayHasKey("id", $responseJson);

        $bookingId = $responseJson["id"];
        $this->get("/booking-confirmed/" . $bookingId)
            ->assertOk()
            ->assertInertia(
                fn(AssertableInertia $page) => $page
                    ->component("BookingConfirmed")
                    ->has("booking")
                    ->where("booking.id", $bookingId)
                    ->where("booking.firstname", "Test")
                    ->where("booking.lastname", "User")
                    ->has("trip")
            );
    }
}
