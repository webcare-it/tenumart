<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Utility\CategoryUtility;

class CategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        // Get language from query or fallback to app locale
        $lang = $request->get('lang', app()->getLocale());
        $fields = $request->get('fields', null);
        
        // Convert fields string to array if provided
        if ($fields) {
            $fields = explode(',', $fields);
        }

        return [
            'data' => $this->collection->map(function ($category) use ($lang, $fields) {
                return $this->formatCategory($category, $lang, $fields);
            }),
        ];
    }

    private function formatCategory($category, $lang, $fields = null)
    {
        // Fetch translation for this category
        $translation = $category->translations
            ->where('lang', $lang)
            ->first();

        $result = [
            'id' => $category->id,
            'name' => $translation ? $translation->name : $category->name,
            'banner' => api_asset($category->banner),
            'icon' => api_asset($category->icon),
            'number_of_children' => CategoryUtility::get_immediate_children_count($category->id),
            'links' => [
                'products' => route('api.products.category', $category->id),
                'sub_categories' => route('subCategories.index', $category->id),
            ],
            'sub_categories' => $category->children
                ? $category->children->map(function ($subCategory) use ($lang, $fields) {
                    $translation = $subCategory->translations
                        ->where('lang', $lang)
                        ->first();

                    return [
                        'id' => $subCategory->id,
                        'name' => $translation ? $translation->name : $subCategory->name,
                        'banner' => api_asset($subCategory->banner),
                        'icon' => api_asset($subCategory->icon),
                        'number_of_children' => CategoryUtility::get_immediate_children_count($subCategory->id),
                        'links' => [
                            'products' => route('api.products.category', $subCategory->id),
                        ],
                        'sub_sub_categories' => $subCategory->children
                            ? $subCategory->children->map(function ($subSubCategory) use ($lang, $fields) {
                                $translation = $subSubCategory->translations
                                    ->where('lang', $lang)
                                    ->first();

                                return [
                                    'id' => $subSubCategory->id,
                                    'name' => $translation ? $translation->name : $subSubCategory->name,
                                    'banner' => api_asset($subSubCategory->banner),
                                    'icon' => api_asset($subSubCategory->icon),
                                    'number_of_children' => CategoryUtility::get_immediate_children_count($subSubCategory->id),
                                    'links' => [
                                        'products' => route('api.products.category', $subSubCategory->id),
                                    ],
                                ];
                            })
                            : [],
                    ];
                })
                : [],
        ];

        // Return only requested fields if specified
        if ($fields) {
            $filtered = [];
            foreach ($fields as $field) {
                if (isset($result[$field])) {
                    $filtered[$field] = $result[$field];
                }
            }
            return $filtered;
        }

        return $result;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200,
        ];
    }
}