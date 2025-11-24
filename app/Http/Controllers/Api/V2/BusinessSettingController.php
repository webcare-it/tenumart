<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BusinessSettingCollection;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Cache;

class BusinessSettingController extends Controller
{
    public function index(Request $request)
    {
        return Cache::remember('app.business_settings', 86400, function() {
            return new BusinessSettingCollection(BusinessSetting::all());
        });
    }
}