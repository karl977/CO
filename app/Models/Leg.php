<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leg extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'price_list_id', 'from_planet_id', 'to_planet_id', 'distance'];

    protected $casts = ['id' => 'string'];

    public function fromPlanet()
    {
        return $this->hasOne(Planet::class, "id", "from_planet_id");
    }

    public function toPlanet()
    {
        return $this->hasOne(Planet::class, "id", "to_planet_id");
    }
}
