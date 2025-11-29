<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\CategoryCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use Cache;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request, $parent_id = 0)
    {
        // Remove language parameter and use default
        if ($request->has('parent_id') && is_numeric($request->get('parent_id'))) {
            $parent_id = $request->get('parent_id');
        }

        // Simplified cache key without language
        $cacheKey = "app.categories_{$parent_id}";

        return Cache::remember($cacheKey, 86400, function() use ($parent_id) {
            // Optimize query with eager loading and select only necessary fields
            $categories = Category::with(['children.children' => function($query) {
                $query->select('id', 'name', 'banner', 'icon', 'parent_id');
            }])
            ->where('parent_id', $parent_id)
            ->select('id', 'name', 'banner', 'icon', 'parent_id')
            ->get();

            return new CategoryCollection($categories);
        });
    }

    public function featured(Request $request)
    {
        $cacheKey = "app.featured_categories";

        return Cache::remember($cacheKey, 86400, function() {
            // Optimize query with select only necessary fields
            $categories = Category::where('featured', 1)
                ->select('id', 'name', 'banner', 'icon')
                ->get();

            return new CategoryCollection($categories);
        });
    }

    public function home(Request $request)
    {
        // Simplified cache key without language
        $cacheKey = 'app.home_categories';

        return Cache::remember($cacheKey, 86400, function() {
            // Optimize query with select only necessary fields
            $homeCategoryIds = json_decode(get_setting('home_categories')) ?: [];
            $categories = Category::whereIn('id', $homeCategoryIds)
                ->select('id', 'name', 'banner', 'icon')
                ->get();

            return new CategoryCollection($categories);
        });
    }

    public function top(Request $request)
    {
        // Simplified cache key without language
        $cacheKey = 'app.top_categories';

        return Cache::remember($cacheKey, 86400, function() {
            // Optimize query with select only necessary fields
            $homeCategoryIds = json_decode(get_setting('home_categories')) ?: [];
            $categories = Category::whereIn('id', $homeCategoryIds)
                ->select('id', 'name', 'banner', 'icon')
                ->limit(20)
                ->get();

            return new CategoryCollection($categories);
        });
    }
}