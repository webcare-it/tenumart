<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\GeneralSettingCollection;
use App\Models\GeneralSetting;
use Cache;

class GeneralSettingController extends Controller
{
    public function index()
    {
        return Cache::remember('app.general_settings', 86400, function() {
            return new GeneralSettingCollection(GeneralSetting::all());
        });
    }

    public function imagePath($id)
    {
        $cacheKey = 'app.image_path_' . $id;
        
        return Cache::remember($cacheKey, 86400, function() use ($id) {
            $upload = \App\Models\Upload::find($id);

            if (!$upload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'id' => $upload->id,
                'file_original_name' => $upload->file_original_name,
                'file_name' => $upload->file_name,
                'url' => uploaded_asset($upload->id),
            ]);
        });
    }
}