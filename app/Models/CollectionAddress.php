<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionAddress extends Model
{
    use HasFactory;
    protected $table='collection_address';  
    protected $fillable=['user_id','name','email','mobile_no','company','address','tracking_no','sender_country','sender_mobile_no','sender_unit_no','sender_area','postcode','sender_region'];

    public function getSourceCountry()
    { 
        return $this->belongsTo('App\Models\Country', 'sender_country', 'id');
    }
}