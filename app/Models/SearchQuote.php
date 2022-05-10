<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchQuote extends Model
{
    use HasFactory;
    protected $table='user_search_quote';  
    protected $fillable=['user_id','quote_id','parcel_id','source_id','destination_id','source_postal','destination_postal','weight'];
    
    // Get Quote Info Data
    public function getQuoteInfo()
    {
        return $this->belongsTo('App\Models\Quote','quote_id','id');
    }
}
