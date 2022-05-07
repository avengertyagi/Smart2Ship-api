<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debitcard extends Model
{
    use HasFactory;
    protected $table='debit_card';  
    protected $fillable=[
    'card_no',
    'expiry_month',
    'expiry_year',
    'cvc',
    'card_holder_name',
];
}
