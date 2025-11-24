<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BrandCollection;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Utility\SearchUtility;
use Cache;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->get('lang', app()->getLocale());
        $name = $request->name;
        
        // Create cache key based on parameters
        $cacheKey = 'app.brands_' . $lang;
        if ($name) {
            $cacheKey .= '_' . md5($name);
        }
        
        return Cache::remember($cacheKey, 3600, function () use ($request, $lang) {
            $brand_query = Brand::query();
            if($request->name != "" || $request->name != null){
                $brand_query->where('name', 'like', '%'.$request->name.'%');
                SearchUtility::store($request->name);
            }
            return new BrandCollection($brand_query->paginate(10));
        });
    }

    public function top(Request $request)
    {
        $lang = $request->get('lang', app()->getLocale());
        $cacheKey = 'app.top_brands_' . $lang;

        return Cache::remember($cacheKey, 86400, function () use ($lang) {
            $brands = Brand::with('translations')
                ->where('top', 1)
                ->get();

            // Attach translated_name dynamically (optional)
            $brands->map(function ($brand) use ($lang) {
                $translation = $brand->translations
                    ->where('lang', $lang)
                    ->first();
                $brand->translated_name = $translation ? $translation->name : $brand->name;
                return $brand;
            });

            return new BrandCollection($brands);
        });
    }
}