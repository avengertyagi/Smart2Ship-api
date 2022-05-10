<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;
    protected $table = 'quote';
    protected $fillable = [
        'courier_code',
        'source_country_id',
        'destination_country_id',
        'source_postcode',
        'destination_postcode',
        'weight',
        'amount',
        'service_type',
        'service_info',
        'courier_company',
        'delivery_duration',
        'delivery_notes',
        'service_rating',
        'status'
    ];

    //Get Source Country
    public function getSourceCountry()
    { 
        return $this->belongsTo('App\Models\Country', 'source_country_id', 'id');
    }

    //Get Destination Country
    public function getDestinationCountry()
    { 
        return $this->belongsTo('App\Models\Country', 'destination_country_id', 'id');
    }
}
