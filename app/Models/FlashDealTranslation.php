<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashDealTranslation extends Model
{
    protected $fillable = ['flash_deal_id', 'lang', 'title'];
    
    public function flashDeal()
    {
        return $this->belongsTo(FlashDeal::class);
    }
}