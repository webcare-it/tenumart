<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\PolicyCollection;
use App\Models\Page;
use Illuminate\Http\Request;
use Cache;

class PolicyController extends Controller
{
    public function sellerPolicy()
    {
        return Cache::remember('app.seller_policy', 86400, function() {
            return new PolicyCollection(Page::where('type', 'seller_policy_page')->get());
        });
    }

    public function supportPolicy()
    {
        return Cache::remember('app.support_policy', 86400, function() {
            return new PolicyCollection(Page::where('type', 'support_policy_page')->get());
        });
    }

    public function returnPolicy()
    {
        return Cache::remember('app.return_policy', 86400, function() {
            return new PolicyCollection(Page::where('type', 'return_policy_page')->get());
        });
    }
}