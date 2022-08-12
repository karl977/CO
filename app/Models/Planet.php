<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planet extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'price_list_id', 'name'];

    protected $casts = ['id' => 'string'];

    protected $hidden = ['price_list_id'];

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }
}
