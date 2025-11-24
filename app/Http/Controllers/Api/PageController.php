<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Http\Resources\PageResource;
use Cache;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cache::remember('app.pages', 86400, function() {
            $pages = Page::all();
            return PageResource::collection($pages);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $cacheKey = 'app.page_' . $slug;
        
        return Cache::remember($cacheKey, 86400, function() use ($slug) {
            $page = Page::where('slug', $slug)->firstOrFail();
            return new PageResource($page);
        });
    }
}