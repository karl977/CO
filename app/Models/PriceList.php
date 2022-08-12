<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'valid_until'];

    protected $casts = [
        'id' => 'string',
        'valid_until' => 'datetime'
    ];
}
