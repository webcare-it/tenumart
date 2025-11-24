<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\SettingsCollection;
use App\Models\AppSettings;
use Cache;

class SettingsController extends Controller
{
    public function index()
    {
        return Cache::remember('app.settings', 86400, function() {
            return new SettingsCollection(AppSettings::all());
        });
    }
}