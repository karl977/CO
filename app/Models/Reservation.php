<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'price_list_id', 'price', 'distance', 'first_name', 'last_name'];

    protected $casts = [
        'id' => 'string'
    ];

    public function providers()
    {
        return $this->belongsToMany(Provider::class, "reservation_provider")->orderBy('order', 'asc');
    }

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }
}
