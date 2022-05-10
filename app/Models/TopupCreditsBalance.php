<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopupCreditsBalance extends Model
{
    use HasFactory;

    protected $table='user_topup_credits_balance';  
    protected $fillable=['user_id','credit','debit','total','package_id','order_id','parcel_id','topup_date','payament_date','description'];
}
