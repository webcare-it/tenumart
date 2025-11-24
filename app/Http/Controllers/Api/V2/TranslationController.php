<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    public function getTranslations($lang = null)
    {
        // Use parameter if provided, otherwise use default language
        $lang = $lang ?? env('DEFAULT_LANGUAGE', 'en');
        $defaultLang = env('DEFAULT_LANGUAGE', 'en');

        // Fetch translations from cache or DB
        $translations = Cache::rememberForever('translations-' . $lang, function () use ($lang) {
            return Translation::where('lang', $lang)->pluck('lang_value', 'lang_key')->toArray();
        });

        // Include default language fallback
        $defaultTranslations = Cache::rememberForever('translations-' . $defaultLang, function () use ($defaultLang) {
            return Translation::where('lang', $defaultLang)->pluck('lang_value', 'lang_key')->toArray();
        });

        return response()->json([
            'lang' => $lang,
            'default' => $defaultLang,
            'translations' => $translations,
            'fallback' => $defaultTranslations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lang' => 'required|string|max:10',
            'lang_value' => 'required|string',
        ]);

        $lang = $request->lang;
        $lang_value = $request->lang_value;
        $lang_key = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', strtolower($lang_value)));

        // Check if this lang_value already exists (unique check)
        $existing = Translation::where('lang_value', $lang_value)->first();

        if ($existing) {
            return response()->json([
                'result' => true,
                'message' => 'Translation already exists',
                'data' => $existing
            ], 200);
        }

        // Create new translation if not found
        $translation = Translation::create([
            'lang' => $lang,
            'lang_key' => $lang_key,
            'lang_value' => $lang_value,
        ]);

        // Clear cache for that language
        Cache::forget('translations-' . $lang);

        return response()->json([
            'result' => true,
            'message' => 'Translation saved successfully',
            'data' => $translation
        ]);
    }
}
