<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\GeneralSettingCollection;
use App\Http\Resources\V2\MetadataCollection;
use App\Models\BusinessSetting;
use App\Models\GeneralSetting as ModelsGeneralSetting;
use Illuminate\Http\Request;
use Cache;

class MetadataController extends Controller
{
    public function index(Request $request)
    {
        return Cache::remember('app.metadata', 86400, function() {
            // Get general settings
            $generalSettings = ModelsGeneralSetting::all();
            
            // Get key business settings for metadata
            $metadataSettings = BusinessSetting::whereIn('type', [
                'system_logo_white',
                'system_logo_black',
                'site_name',
                'site_motto',
                'site_icon',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'meta_image',
                'default_currency',
                'currency_symbol',
                'show_currency_symbol'
            ])->get();
            
            // Combine settings into a single collection
            $allSettings = $generalSettings->merge($metadataSettings);
            
            return new MetadataCollection($allSettings);
        });
    }
    
    public function getSiteInfo(Request $request)
    {
        return Cache::remember('app.site_info', 86400, function() {
            $metaImage = get_setting('meta_image');
            $metaImageUrl = $metaImage ? api_asset($metaImage) : null;
            
            $siteInfo = [
                'name' => get_setting('site_name'),
                'motto' => get_setting('site_motto'),
                'logo' => get_setting('system_logo_white'),
                'favicon' => get_setting('site_icon'),
                'description' => get_setting('meta_description'),
                'keywords' => get_setting('meta_keywords'),
                'currency' => get_setting('default_currency'),
                'currency_symbol' => get_setting('currency_symbol'),
                'meta_image' => $metaImage,
                'meta_image_url' => $metaImageUrl
            ];
            
            return response()->json([
                'data' => $siteInfo,
                'success' => true,
                'status' => 200
            ]);
        });
    }
    
    public function getMetaImage(Request $request)
    {
        return Cache::remember('app.meta_image', 86400, function() {
            $metaImage = get_setting('meta_image');
            $metaImageUrl = $metaImage ? api_asset($metaImage) : null;
            
            $metaData = [
                'meta_image' => $metaImage,
                'meta_image_url' => $metaImageUrl,
                'meta_title' => get_setting('meta_title'),
                'meta_description' => get_setting('meta_description'),
                'meta_keywords' => get_setting('meta_keywords')
            ];
            
            return response()->json([
                'data' => $metaData,
                'success' => true,
                'status' => 200
            ]);
        });
    }
}