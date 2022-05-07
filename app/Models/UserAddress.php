<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table='user_address_information';  
    protected $fillable=['user_id','contact_name','mobile_no','email','alternate_no','company_name','country','address','area','postal_code','region','is_primary'];
}
