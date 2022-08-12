<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Trip;
use App\Models\Company;
use App\Models\Leg;
use App\Models\Planet;
use App\Models\PriceList;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TripsController extends Controller
{
    /** @var PriceList being used for retrieving other objects */
    private PriceList $priceList;

    /** @var array containing connections that each planet has to another planets */
    private array $planetLegs = [];

    /** @var array contains all routes */
    private array $routes = [];

    /** @var array Contains trips to be returned */
    private array $trips = [];

    public function __construct()
    {
        $this->priceList = PriceList::where("valid_until", ">=", Carbon::now())->first();
    }

    /**
     * Finds trips based on from and to planet, companies filter, sort parameter and limit
     *
     * @param Planet $fromPlanet
     * @param Planet $toPlanet
     * @param array $filterCompanies
     * @param string $sortByKey
     * @param int $limit
     * @return array
     */
    public function findTrips(Planet $fromPlanet, Planet $toPlanet, array $filterCompanies, string $sortByKey, int $limit = 30)
    {

        // Clear values
        $this->planetLegs = [];
        $this->routes = [];
        $this->trips = [];

        // Find all legs for each planet
        // Store the result in $this->planetLegs
        $this->findPlanetLegs();

        // Find all possible routes from source planet to destination planet
        // Store the arrays of legs in $this->routes
        $this->findLegRoutesRecursive($fromPlanet->id, $toPlanet->id, []);

        // Find all possible trips for each providers combinations for given legs
        // Store the result in $this->trips
        $this->findTripsFromLegRoutes();

        // Calculate total trip distance, price, etc.
        foreach ($this->trips as $trip) {
            $trip->calculateParameters();
        }

        // Fill in data to be returned as json to front-end
        foreach ($this->trips as $trip) {
            $trip->fillProvidersArrayFromModels();
        };

        // Fill in unique ids for each trip
        foreach ($this->trips as $trip) {
            $trip->id = Str::uuid();
        };

        // Calculate scores for each trip in $this->trips based on duration and price
        $this->calculateTripScores();

        // Sort $this->trips by provided argument
        $this->sortTrips($sortByKey);

        // Filter by companies if required
        $this->filterTripsByCompanies($filterCompanies);

        // Leave only trips that are in future
        $this->filterTripsFuture();

        // Limit values
        return array_slice($this->trips, 0, $limit, true);
    }

    /**
     * Filter trips by companies models
     * Stores the result in $this->trips
     *
     * @param array $companies
     * @return void
     */
    public function filterTripsByCompanies(array $companies): void
    {
        if (count($companies) === 0) {
            return;
        }
        $companyIds = [];
        foreach ($companies as $company) {
            $companyIds = [...$companyIds, $company->id];
        }

        $filteredTrips = [];
        foreach ($this->trips as $trip) {
            $allCompaniesPresent = true;

            foreach ($trip->getProviderModels() as $provider) {
                if (!in_array($provider->company_id, $companyIds)) {
                    $allCompaniesPresent = false;
                    break;
                }
            }

            if ($allCompaniesPresent) {
                $filteredTrips = [...$filteredTrips, $trip];
            }
        }
        $this->trips = $filteredTrips;
    }

    /**
     * Filter trips by leaving only trips that start in the future
     * Stores the result in $this->trips
     *
     * @return void
     */
    public function filterTripsFuture()
    {
        $filteredTrips = [];
        foreach ($this->trips as $trip) {
            if ($trip->getStart()->isFuture()) {
                $filteredTrips = [...$filteredTrips, $trip];
            }
        }
        $this->trips = $filteredTrips;
    }

    /**
     * Sort results by given sort key
     * Stores the result in $this->trips
     *
     * @param string $sort
     * @return void
     */
    public function sortTrips(string $sort): void
    {
        switch ($sort) {
            case "price":
                usort($this->trips, array($this, "sortByPrice"));
                break;
            case "distance":
                usort($this->trips, array($this, "sortByDistance"));
                break;
            case "duration":
                usort($this->trips, array($this, "sortByDuration"));
                break;
            case "best":
            default:
                usort($this->trips, array($this, "sortByScore"));
                break;
        }
    }

    /**
     * Sort function for sorting by score
     *
     * @param Trip $tripA
     * @param Trip $tripB
     * @return int
     */
    private function sortByScore(Trip $tripA, Trip $tripB): int
    {
        if ($tripA->getScore() > $tripB->getScore()) {
            return -1;
        } elseif ($tripA->getScore() < $tripB->getScore()) {
            return 1;
        } elseif ($tripA->getScore() == $tripB->getScore()) {
            return 0;
        } else {
            return 0;
        }
    }

    /**
     * Sort function for sorting by distance
     *
     * @param Trip $tripA
     * @param Trip $tripB
     * @return int
     */
    private function sortByDistance(Trip $tripA, Trip $tripB): int
    {
        if ($tripA->getDistance() > $tripB->getDistance()) {
            return 1;
        } elseif ($tripA->getDistance() < $tripB->getDistance()) {
            return -1;
        } elseif ($tripA->getDistance() == $tripB->getDistance()) {
            return 0;
        } else {
            return 0;
        }
    }

    /**
     * Sort function for sorting by price
     *
     * @param Trip $tripA
     * @param Trip $tripB
     * @return int
     */
    private function sortByPrice(Trip $tripA, Trip $tripB): int
    {
        if ($tripA->getPrice() > $tripB->getPrice()) {
            return 1;
        } elseif ($tripA->getPrice() < $tripB->getPrice()) {
            return -1;
        } elseif ($tripA->getPrice() == $tripB->getPrice()) {
            return 0;
        } else {
            return 0;
        }
    }

    /**
     * Sort function for sorting by duration
     *
     * @param Trip $tripA
     * @param Trip $tripB
     * @return int
     */
    private function sortByDuration(Trip $tripA, Trip $tripB): int
    {
        if ($tripA->getDuration() > $tripB->getDuration()) {
            return 1;
        } elseif ($tripA->getDuration() < $tripB->getDuration()) {
            return -1;
        } elseif ($tripA->getDuration() == $tripB->getDuration()) {
            return 0;
        } else {
            return 0;
        }
    }

    /**
     * Find legs for each planet
     * Stores the result in $this->planetLegs
     *
     * @return void
     */
    public function findPlanetLegs(): void
    {
        $legs = Leg::where("price_list_id", $this->priceList->id)->get();
        $this->planetLegs = [];
        foreach ($legs as $currentLeg) {
            if (!array_key_exists($currentLeg->from_planet_id, $this->planetLegs)) {
                $this->planetLegs[$currentLeg->from_planet_id] = [];
            }
            array_push($this->planetLegs[$currentLeg->from_planet_id], $currentLeg);
        }
    }

    /**
     * Calculate scores for each trip based on duration and price
     * Stores the result in $this->planetLegs
     *
     * @return void
     */
    public function calculateTripScores(): void
    {
        $duration_min = 2147483647;
        $duration_max = 0;
        $price_min = 2147483647;
        $price_max = 0;

        foreach ($this->trips as $trip) {
            if ($trip->getDuration() < $duration_min) {
                $duration_min = $trip->getDuration();
            }
            if ($trip->getDuration() > $duration_max) {
                $duration_max = $trip->getDuration();
            }
            if ($trip->getPrice() < $price_min) {
                $price_min = $trip->getPrice();
            }
            if ($trip->getPrice() > $price_max) {
                $price_max = $trip->getPrice();
            }
        }

        foreach ($this->trips as $trip) {
            $duration_score = ($duration_max - $trip->getDuration()) / ($duration_max - $duration_min);
            $price_score = ($price_max - $trip->getPrice()) / ($price_max - $price_min);
            $trip->setScore(($duration_score + $price_score) / 2);
        }
    }

    /**
     * Find trips from routes found by findLegRoutesRecursive
     * Stores the result in $this->trips
     *
     * @return void
     */
    public function findTripsFromLegRoutes()
    {
        $this->trips = [];
        foreach ($this->routes as $routeLegs) {

            $fromTimestamp = Carbon::now();

            $currentLeg = $routeLegs[0];
            $nextLegs = $routeLegs;
            array_splice($nextLegs, 0, 1);

            $trip = new Trip();

            $this->findTripsFromLegRoutesRecursive($currentLeg, $fromTimestamp, $nextLegs, $trip);
        }
    }

    /**
     * Recursive function for finding trips
     * Stores the result in $this->trips
     *
     * @param Leg $currentLeg
     * @param Carbon $lastLegEndTimestamp
     * @param array $nextLegs
     * @param Trip $trip
     * @return void
     */
    public function findTripsFromLegRoutesRecursive(Leg $currentLeg, Carbon $lastLegEndTimestamp, array $nextLegs, Trip $trip): void
    {

        $nextLegId = null;
        $nextLegsNew = null;
        if (count($nextLegs) > 0) {
            $nextLegId = $nextLegs[0];
            $nextLegsNew = $nextLegs;
            array_splice($nextLegsNew, 0, 1);
        }

        $providers = Provider::where("price_list_id", $this->priceList->id)->where("leg_id", $currentLeg->id)->where("flight_start", ">=", $lastLegEndTimestamp)->get();

        foreach ($providers as $provider) {
            $newTrip = clone $trip;
            $newTrip->addProviderModel($provider);

            if (is_null($nextLegId)) {
                array_push($this->trips, $newTrip);
            } else {
                $this->findTripsFromLegRoutesRecursive($nextLegId, $provider->flight_end, $nextLegsNew, $newTrip);
            }
        }
    }

    /**
     * Recursive function for finding trips
     * Stores the result in $this->trips
     *
     * @param string $currentPlanetId
     * @param string $destinationPlanetId
     * @param array $pathList
     * @param array $visitedPlanetIds
     * @return void
     */
    public function findLegRoutesRecursive(string $currentPlanetId, string $destinationPlanetId, array $pathList, array $visitedPlanetIds = []): void
    {

        // Arrived at destination. Save path
        if ($currentPlanetId === $destinationPlanetId) {
            array_push($this->routes, $pathList);
        }

        // Set current planet visited
        array_push($visitedPlanetIds, $currentPlanetId);

        if (!isset($this->planetLegs[$currentPlanetId])) {
            $this->planetLegs[$currentPlanetId] = [];
        }

        // Go through all adjacent planets that are not visited
        foreach ($this->planetLegs[$currentPlanetId] as $planetLeg) {
            if (!in_array($planetLeg->to_planet_id, $visitedPlanetIds)) {
                array_push($pathList, $planetLeg);
                $this->findLegRoutesRecursive($planetLeg->to_planet_id, $destinationPlanetId, $pathList, $visitedPlanetIds);
                if (($key = array_search($planetLeg, $pathList)) !== false) {
                    array_splice($pathList, $key, 1);
                }
            }
        }
        // Remove current planet from visited planets list
        if (($key = array_search($currentPlanetId, $visitedPlanetIds)) !== false) {
            array_splice($visitedPlanetIds, $key, 1);

        }
    }
}
