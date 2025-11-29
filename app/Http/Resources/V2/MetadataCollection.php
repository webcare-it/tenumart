<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MetadataCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                // Handle different model types
                if ($data instanceof \App\Models\GeneralSetting) {
                    return [
                        'type' => 'general_setting',
                        'logo' => $data->logo,
                        'site_name' => $data->site_name,
                        'address' => $data->address,
                        'description' => $data->description,
                        'phone' => $data->phone,
                        'email' => $data->email,
                        'facebook' => $data->facebook,
                        'twitter' => $data->twitter,
                        'instagram' => $data->instagram,
                        'youtube' => $data->youtube,
                        'google_plus' => $data->google_plus
                    ];
                } elseif ($data instanceof \App\Models\BusinessSetting) {
                    // Handle meta image
                    $metaImage = null;
                    $metaImageUrl = null;
                    if ($data->type === 'meta_image' && $data->value) {
                        $metaImage = $data->value;
                        $metaImageUrl = api_asset($metaImage);
                    }
                    
                    return [
                        'type' => 'business_setting',
                        'setting_type' => $data->type,
                        'value' => $data->value,
                        'meta_image' => $metaImage,
                        'meta_image_url' => $metaImageUrl
                    ];
                }
                
                return $data;
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}