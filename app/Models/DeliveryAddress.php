<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $table='delivery_address';  
    protected $fillable=['user_id','name','email','mobile_no','company','address','tracking_no','receiver_company','receiver_country','receiver_mobile_no','receiver_unit_no','receiver_unit_no','receiver_area','receiver_region','postcode'];

     // Get Delivery Address Data
     public function getDestinationCountry()
     {
         return $this->belongsTo('App\Models\Country','receiver_country','id');
     }
}
