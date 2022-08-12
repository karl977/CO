<?php

namespace App\Http\Helpers;

use App\Models\Provider;
use Illuminate\Support\Str;

class Trip
{

    public string $id;
    public int $distance = 0;
    public float $price = 0;
    public int $duration = 0;
    public $start = null;
    public $end = null;
    private float $score = 0;
    private array $providerModels = [];
    public array $providers = [];

    /**
     * Calculates parameters based on providers in the trip
     *
     * @return void
     */
    public function calculateParameters(): void
    {
        for($i = 0; $i < count($this->providerModels); $i++){
            $provider = $this->providerModels[$i];
            $leg = $provider->leg()->first();
            $this->addDistance($leg->distance);
            $this->addPrice($provider->price);
            if($i === 0) {
                $this->addDuration($provider->flight_start->diffInSeconds($provider->flight_end));
            }else{
                $prevProvider = $this->providerModels[$i-1];
                $this->addDuration($prevProvider->flight_end->diffInSeconds($provider->flight_end));
            }

            if ($i === 0) {
                $this->setStart($provider->flight_start);
            }
            if ($i === count($this->providerModels) - 1) {
                $this->setEnd($provider->flight_end);
            }
        }

    }

    /**
     * Fill providers array from provider models
     *
     * @return void
     */
    public function fillProvidersArrayFromModels(){
        foreach ($this->providerModels as $provider){
            $arr = [];
            $leg = $provider->leg()->first();
            $arr["id"] = $provider->id;
            $arr["flight_start"] = $provider->flight_start;
            $arr["flight_end"] = $provider->flight_end;
            $arr["duration"] = $provider->flight_start->diffInSeconds($provider->flight_end);
            $arr["company_name"] =  $provider->company()->first()->name;
            $arr["from_planet_name"] = $leg->fromPlanet()->first()->name;
            $arr["to_planet_name"] = $leg->toPlanet()->first()->name;
            $this->providers = [...$this->providers, $arr];
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * @param int $distance
     */
    public function setDistance(int $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @param int $distance
     */
    public function addDistance(int $distance): void
    {
        $this->distance += $distance;
    }

    /**
     * @return float|int
     */
    public function getPrice(): float|int
    {
        return $this->price;
    }

    /**
     * @param float|int $price
     */
    public function setPrice(float|int $price): void
    {
        $this->price = $price;
    }

    /**
     * @param float|int $price
     */
    public function addPrice(float|int $price): void
    {
        $this->price += $price;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @param int $duration
     */
    public function addDuration(int $duration): void
    {
        $this->duration += $duration;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @param float $score
     */
    public function setScore(float $score): void
    {
        $this->score = $score;
    }

    /**
     * @return array
     */
    public function getProviderModels(): array
    {
        return $this->providerModels;
    }

    /**
     * @param array $providerModels
     */
    public function setProviderModels(array $providerModels): void
    {
        $this->providerModels = $providerModels;
    }

    /**
     * @param Provider $provider
     */
    public function addProviderModel(Provider $provider): void
    {
        $this->providerModels = [...$this->providerModels, $provider];
    }

    /**
     * @return null
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param null $start
     */
    public function setStart($start): void
    {
        $this->start = $start;
    }

    /**
     * @return null
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param null $end
     */
    public function setEnd($end): void
    {
        $this->end = $end;
    }

    /**
     * @return array
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param array $providers
     */
    public function setProviders(array $providers): void
    {
        $this->providers = $providers;
    }
}
