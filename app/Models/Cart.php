<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table='user_cart_list';  
    protected $fillable=['user_id','parcel_id','quote_id','original_price','weight','courier_from_detail_id','courier_to_detail_id','price'];

    // Get Quote Info Data
    public function getQuoteInfo()
    {
        return $this->belongsTo('App\Models\Quote','quote_id','id');
    }

    // Get Parcel Detail Data
    public function getParcelDetail()
    {
        return $this->belongsTo('App\Models\ParcelDetail','parcel_id','id');
    }

    // Get Collection Address Data
    public function getCollectionAddress()
    {
        return $this->belongsTo('App\Models\CollectionAddress','courier_from_detail_id','id')->with('getSourceCountry:id,country_name,phone_code,iso_code');
    }

    // Get Delivery Address Data
    public function getDeliveryAddress()
    {
        return $this->belongsTo('App\Models\DeliveryAddress','courier_to_detail_id','id')->with('getDestinationCountry');
    }
}
