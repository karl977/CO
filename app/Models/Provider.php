<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'price_list_id', 'company_id', 'leg_id', 'price', 'flight_start', 'flight_end'];

    protected $casts = [
        'id' => 'string',
        'flight_start' => 'datetime',
        'flight_end' => 'datetime'
    ];

    public function leg()
    {
        return $this->belongsTo(Leg::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, "reservation_provider");
    }
}
