<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Trip;
use App\Models\Company;
use App\Models\Planet;
use App\Models\PriceList;
use App\Models\Provider;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;

class BookingController extends Controller
{

    /**
     * Booking page
     *
     * @return Response
     */
    public function index(): Response
    {

        $priceList = PriceList::where("valid_until", ">=", Carbon::now())->first();

        $priceListId = is_null($priceList) ? Str::uuid() : $priceList->id;

        $planets = Planet::where("price_list_id", $priceListId)->get();
        $companies = Company::where("price_list_id", $priceListId)->get();

        return Inertia::render('Booking', [
            "title" => "Booking",
            "planets" => $planets,
            "companies" => $companies
        ]);
    }

    /**
     * Booking page that launches search based on planets, sorting & filtering in URL
     *
     * @param Request $request
     * @param string $fromPlantName
     * @param string $toPlanetName
     * @return Response
     */
    public function search(Request $request, string $fromPlantName, string $toPlanetName)
    {

        $priceList = PriceList::where("valid_until", ">=", Carbon::now())->first();

        $priceListId = is_null($priceList) ? Str::uuid() : $priceList->id;

        $search = [
            "fromPlanetName" => $fromPlantName,
            "toPlanetName" => $toPlanetName,
        ];

        $sort = $request->input('sort');
        if (isset($sort)) {
            $search["sort"] = $sort;
        }

        $companies = $request->input('companies');
        if (isset($companies)) {
            $search["companies"] = $companies;
        }

        $planets = Planet::where("price_list_id", $priceListId)->get();
        $companies = Company::where("price_list_id", $priceListId)->get();

        return Inertia::render('Booking', [
            "title" => "Booking",
            "planets" => $planets,
            "companies" => $companies,
            "search" => $search
        ]);
    }

    /**
     * Confirm booking page
     *
     * @param string $id Id of the trip stored in session
     * @return Response
     */
    public function confirmPage(string $id): Response
    {

        $storedTrip = Session::get("trip-" . $id);

        if (!is_null($storedTrip)) {
            return Inertia::render('BookingConfirm', [
                "title" => "Confirm booking",
                "trip" => $storedTrip,
                "trip_id" => $id
            ]);
        } else {
            return Inertia::render('BookingConfirm', [
                "title" => "Confirm booking",
                "error" => "Reservation not found"
            ]);
        }

    }

    /**
     * Booking confirmed page
     *
     * @param string $id Id of the reservation stored in database
     * @return Response
     */
    public function confirmedPage(string $id): Response
    {

        $reservation = Reservation::where("id", $id)->first();

        if (is_null($reservation)) {
            return Inertia::render('BookingConfirmed', [
                "title" => "Your booking",
                "error" => "Reservation not found"
            ]);
        }

        $trip = new Trip();

        foreach ($reservation->providers as $provider) {
            $trip->addProviderModel($provider);
        }
        $trip->calculateParameters();
        $trip->fillProvidersArrayFromModels();

        return Inertia::render('BookingConfirmed', [
            "title" => "Your booking",
            "booking" => [
                "id" => $reservation->id,
                "firstname" => $reservation->first_name,
                "lastname" => $reservation->last_name
            ],
            "trip" => $trip
        ]);
    }

    /**
     * Search trip API endpoint that returns possible trips based on given parameters
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchTrips(Request $request): JsonResponse
    {

        $priceList = PriceList::where("valid_until", ">=", Carbon::now())->first();

        if (is_null($priceList)) {
            return response()->json(["error" => "Schedules unavailable. Please try again later."])->setStatusCode(400);
        }

        $fromPlanetName = $request->input("fromPlanetName");
        $toPlanetName = $request->input("toPlanetName");
        $sort = $request->input("sort");
        $companiesRaw = $request->input("companies");

        $fromPlanet = Planet::where("price_list_id", $priceList->id)->where("name", $fromPlanetName)->first();
        if (is_null($fromPlanet)) {
            return response()->json(["error" => "Invalid request parameters"])->setStatusCode(400);
        }

        $toPlanet = Planet::where("price_list_id", $priceList->id)->where("name", $toPlanetName)->first();
        if (is_null($toPlanet)) {
            return response()->json(["error" => "Invalid request parameters"])->setStatusCode(400);
        }

        if (is_null($sort)) {
            $sort = "";
        }

        $companies = [];
        if (!is_null($companiesRaw)) {
            $companyNames = explode(",", $companiesRaw);
            foreach ($companyNames as $companyName) {
                $company = Company::where("price_list_id", $priceList->id)->where("name", $companyName)->first();
                if (!is_null($company)) {
                    $companies = [...$companies, $company];
                }
            }
        }

        $routes = app('App\Http\Controllers\TripsController')->findTrips($fromPlanet, $toPlanet, $companies, $sort);

        return response()->json($routes);
    }

    /**
     * Select trip API endpoint that allows selecting trip and stores it in session
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function selectTrip(Request $request): JsonResponse
    {

        $priceList = PriceList::where("valid_until", ">=", Carbon::now())->first();

        if (is_null($priceList)) {
            return response()->json(["error" => "Providers expired or invalid trip. Re-do the search and try again"])->setStatusCode(400);;
        }

        $trip = $request->json()->all();

        $allProvidersValid = $this->validateProviders($trip, $priceList);

        if (!$allProvidersValid) {
            return response()->json(["error" => "Providers expired or invalid trip. Re-do the search and try again"])->setStatusCode(400);;
        }

        $uuid = Str::uuid();

        Session::put("trip-" . $uuid, $trip);

        return response()->json(["id" => $uuid]);
    }

    /**
     * Validate providers in given trip
     *
     * @param array $trip
     * @param PriceList $priceList
     * @return boolean Whether providers of the given trip are valid or not
     */
    public function validateProviders(array $trip, PriceList $priceList): bool
    {
        $lastProviderEnd = Carbon::now();
        $lastPlanet = false;

        $firstProviderId = $trip["providers"][0];
        $firstProvider = Provider::where("price_list_id", $priceList->id)->where("id", $firstProviderId)->first();
        if (is_null($firstProvider)) {
            return false;
        }

        if (Carbon::now()->isAfter($firstProvider->flight_start)) {
            return false;
        }

        foreach ($trip["providers"] as $providerId) {
            $provider = Provider::where("price_list_id", $priceList->id)->where("id", $providerId)->first();
            if (is_null($provider)) {
                return false;
            }
            if ($lastProviderEnd->isAfter($provider->flight_start)) {
                return false;
            }

            $leg = $provider->leg()->first();
            if (is_null($leg)) {
                return false;
            }

            $fromPlanet = $leg->fromPlanet()->first();
            if (is_null($fromPlanet)) {
                return false;
            }

            if ($lastPlanet !== false) {
                if ($lastPlanet->id !== $fromPlanet->id) {
                    return false;
                }
            }
            $lastProviderEnd = $provider->flight_end;

            $lastPlanet = $leg->toPlanet()->first();
            if (is_null($lastPlanet)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate providers in given trip
     *
     * @param Request $request
     * @param string $storedTripId
     * @return JsonResponse
     */
    public function confirmTrip(Request $request, string $storedTripId): JsonResponse
    {

        $priceList = PriceList::where("valid_until", ">=", Carbon::now())->first();

        if (is_null($priceList)) {
            return response()->json(["error" => "Booking expired. Please select a new trip and try again"])->setStatusCode(400);
        }

        $allProvidersValid = true;
        $body = $request->json()->all();

        if (!isset($storedTripId)) {
            return response()->json(["error" => "Invalid request"])->setStatusCode(400);;
        }

        $trip = Session::get("trip-" . $storedTripId);

        if (is_null($trip)) {
            return response()->json(["error" => "Trip not found"])->setStatusCode(400);;
        }

        foreach ($trip["providers"] as $providerId) {
            $provider = Provider::where("price_list_id", $priceList->id)->where("id", $providerId)->first();
            if (is_null($provider)) {
                $allProvidersValid = false;
                break;
            }
        }

        if (!$allProvidersValid) {
            return response()->json(["error" => "Booking expired. Please select a new trip and try again"])->setStatusCode(400);
        }

        if (!isset($body['first_name']) || strlen($body['first_name']) === 0) {
            return response()->json(["error" => "No first name provided"])->setStatusCode(400);
        }

        if (!isset($body['last_name']) || strlen($body['last_name']) === 0) {
            return response()->json(["error" => "No last name provided"])->setStatusCode(400);
        }

        $reservationId = Str::uuid();

        $reservation = new Reservation();
        $reservation->id = $reservationId;
        $reservation->first_name = $body['first_name'];
        $reservation->last_name = $body['last_name'];
        $reservation->price = $trip["price"];
        $reservation->distance = $trip["distance"];
        $reservation->priceList()->associate($priceList);

        $reservation->save();

        $reservation = Reservation::where("price_list_id", $priceList->id)->where("id", $reservationId)->first();

        for ($i = 0; $i < count($trip["providers"]); $i++) {
            $providerId = $trip["providers"][$i]["id"];
            $reservation->providers()->attach($providerId, ["order" => $i]);
        }

        Session::remove("trip-" . $storedTripId);

        return response()->json(["id" => $reservation->id]);
    }
}
