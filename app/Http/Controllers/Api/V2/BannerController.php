<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BannerCollection;
use Cache;

class BannerController extends Controller
{

    public function index()
    {
        return Cache::remember('app.home_banner_images', 86400, function(){
            return new BannerCollection(json_decode(get_setting('home_banner1_images'), true));
        });
    }
}