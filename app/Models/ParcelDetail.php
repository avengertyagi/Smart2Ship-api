<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelDetail extends Model
{
    use HasFactory;

    protected $table='parcel_detail';  
    protected $fillable=['user_id','parcel_id','type','category','remark','content','value','weight','tracking_no','pickup_date'];
}
