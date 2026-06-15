<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'age', 
        'currency_id', 
        'start_date', 
        'end_date',
        'trip_length',
        'total_cost'
    ];
}
