<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BrandCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $lang = $request->get('lang', app()->getLocale());
        $fields = $request->get('fields', null);
        
        // Convert fields string to array if provided
        if ($fields) {
            $fields = explode(',', $fields);
        }

        return [
            'data' => $this->collection->map(function ($brand) use ($lang, $fields) {
                $translation = $brand->translations
                    ->where('lang', $lang)
                    ->first();

                $result = [
                    'id' => $brand->id,
                    'name' => $translation ? $translation->name : $brand->name,
                    'logo' => api_asset($brand->logo),
                    'links' => [
                        'products' => route('api.products.brand', $brand->id)
                    ],
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
            }),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200,
        ];
    }
}