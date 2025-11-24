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
        // Determine language and parent category
        $lang = $request->get('lang', app()->getLocale());
        if ($request->has('parent_id') && is_numeric($request->get('parent_id'))) {
            $parent_id = $request->get('parent_id');
        }

        // Cache key includes both parent ID and language
        $cacheKey = "app.categories_{$parent_id}_{$lang}";

        return Cache::remember($cacheKey, 86400, function() use ($parent_id, $lang) {
            $categories = Category::with([
                'translations',
                'children.translations',
                'children.children.translations'
            ])
                ->where('parent_id', $parent_id)
                ->get();

            // Attach translated names
            $categories->map(function ($category) use ($lang) {
                $translation = $category->translations
                    ->where('lang', $lang)
                    ->first();

                $category->translated_name = $translation ? $translation->name : $category->name;
                return $category;
            });

            return new CategoryCollection($categories);
        });
    }

    public function featured(Request $request)
    {
        $lang = $request->get('lang', app()->getLocale());
        $cacheKey = "app.featured_categories_{$lang}";

        return Cache::remember($cacheKey, 86400, function() use ($lang) {
            $categories = Category::with([
                'translations',
                'children.translations',
                'children.children.translations'
            ])
                ->where('featured', 1)
                ->get();

            $categories->map(function ($category) use ($lang) {
                $translation = $category->translations
                    ->where('lang', $lang)
                    ->first();

                $category->translated_name = $translation ? $translation->name : $category->name;
                return $category;
            });

            return new CategoryCollection($categories);
        });
    }

    public function home(Request $request)
    {
        $lang = $request->get('lang', app()->getLocale());

        // Cache key should include language
        $cacheKey = 'app.home_categories_' . $lang;

        return Cache::remember($cacheKey, 86400, function() use ($lang) {
            // Preload all translations and nested children translations to avoid N+1 queries
            $categories = Category::with([
                'translations',
                'children.translations',
                'children.children.translations'
            ])
                ->whereIn('id', json_decode(get_setting('home_categories')))
                ->get();

            // Pass categories to resource
            return new CategoryCollection($categories->map(function($category) use ($lang) {
                // Filter translations by requested lang
                $translation = $category->translations
                    ->where('lang', $lang)
                    ->first();

                // Set a temporary attribute for the current language
                $category->translated_name = $translation ? $translation->name : $category->name;

                return $category;
            }));
        });
    }

    public function top(Request $request)
    {
        // Get requested language or fallback
        $lang = $request->get('lang', app()->getLocale());

        // Create a language-specific cache key
        $cacheKey = 'app.top_categories_' . $lang;

        return Cache::remember($cacheKey, 86400, function() use ($lang) {
            // Preload translations and child categories
            $categories = Category::with([
                'translations',
                'children.translations',
                'children.children.translations'
            ])
                ->whereIn('id', json_decode(get_setting('home_categories')))
                ->limit(20)
                ->get();

            // Attach translated names before sending to resource
            $categories->map(function ($category) use ($lang) {
                $translation = $category->translations
                    ->where('lang', $lang)
                    ->first();

                // Temporarily attach a translated_name attribute
                $category->translated_name = $translation ? $translation->name : $category->name;

                return $category;
            });

            return new CategoryCollection($categories);
        });
    }
}
