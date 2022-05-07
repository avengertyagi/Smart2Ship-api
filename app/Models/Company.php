<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table='companies_information';  
    protected $fillable=[
        'company_name',
    	'company_reg_no',
        'facebook_link',
        'instagram_link',
        'twitter_link',
        'nature_bussiness',
        'selling_channel',
        'gst_no'];
}
