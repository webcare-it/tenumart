<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V2\ProductDetailCollection;
use App\Http\Resources\V2\FlashDealCollection;
use App\Models\FlashDeal;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Utility\CategoryUtility;
use App\Utility\SearchUtility;
use Cache;

class ProductController extends Controller
{
    public function index()
    {
        return Cache::remember('app.products_latest', 3600, function() {
            return new ProductMiniCollection(Product::latest()->paginate(10));
        });
    }

    public function show($id)
    {
        return Cache::remember("app.product_$id", 86400, function() use ($id) {
            return new ProductDetailCollection(Product::where('id', $id)->get());
        });
    }

    public function admin()
    {
        return Cache::remember('app.products_admin', 3600, function() {
            return new ProductCollection(Product::where('added_by', 'admin')->latest()->paginate(10));
        });
    }

    public function seller($id, Request $request)
    {
        $name = $request->name;
        $cacheKey = "app.products_seller_$id";
        if ($name) {
            $cacheKey .= '_' . md5($name);
        }
        
        return Cache::remember($cacheKey, 3600, function() use ($id, $request) {
            $shop = Shop::findOrFail($id);
            $products = Product::where('added_by', 'seller')->where('user_id', $shop->user_id);
            if ($request->name != "" || $request->name != null) {
                $products = $products->where('name', 'like', '%' . $request->name . '%');
            }
            $products->where('published', 1);
            return new ProductMiniCollection($products->latest()->paginate(10));
        });
    }

    public function category($id, Request $request)
    {
        $name = $request->name;
        $cacheKey = "app.products_category_$id";
        if ($name) {
            $cacheKey .= '_' . md5($name);
        }
        
        return Cache::remember($cacheKey, 3600, function() use ($id, $request) {
            $category_ids = CategoryUtility::children_ids($id);
            $category_ids[] = $id;

            $products = Product::whereIn('category_id', $category_ids);

            if ($request->name != "" || $request->name != null) {
                $products = $products->where('name', 'like', '%' . $request->name . '%');
            }
            $products->where('published', 1);
            return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
        });
    }


    public function brand($id, Request $request)
    {
        $name = $request->name;
        $cacheKey = "app.products_brand_$id";
        if ($name) {
            $cacheKey .= '_' . md5($name);
        }
        
        return Cache::remember($cacheKey, 3600, function() use ($id, $request) {
            $products = Product::where('brand_id', $id);
            if ($request->name != "" || $request->name != null) {
                $products = $products->where('name', 'like', '%' . $request->name . '%');
            }

            return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
        });
    }

    public function todaysDeal()
    {
        $products = Product::where('todays_deal', 1);
        return new ProductMiniCollection(filter_products($products)->limit(20)->latest()->get());
    }

    public function flashDeal()
    {
        return Cache::remember('app.flash_deals', 86400, function(){
            $flash_deals = FlashDeal::where('status', 1)->where('featured', 1)->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->get();
            return new FlashDealCollection($flash_deals);
        });
    }

    public function featured()
    {
        return Cache::remember('app.featured_products', 3600, function() {
            $products = Product::where('featured', 1);
            return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
        });
    }

    public function bestSeller()
    {
        return Cache::remember('app.best_selling_products', 86400, function(){
            $products = Product::orderBy('num_of_sale', 'desc');
            return new ProductMiniCollection(filter_products($products)->limit(20)->get());
        });
    }

    public function homeProducts()
    {
        $products = Product::where('todays_deal', 1);
            $todays_deal = new ProductMiniCollection(filter_products($products)->limit(20)->latest()->get());

            $flash_dealss = FlashDeal::where('status', 1)->where('featured', 1)->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->get();
            $flash_deals = new FlashDealCollection($flash_dealss);

            $fproducts = Product::where('featured', 1);
            $featured = new ProductMiniCollection(filter_products($fproducts)->latest()->paginate(10));

             $bproducts = Product::orderBy('num_of_sale', 'desc');
             $best_selling = new ProductMiniCollection(filter_products($bproducts)->limit(20)->get());

             return [
                'todays_deal' => $todays_deal,
                'flash_deals' => $flash_deals,
                'featured' => $featured,
                'best_selling' => $best_selling,
            ];
    }

    public function related($id)
    {
        return Cache::remember("app.related_products-$id", 86400, function() use ($id){
            $product = Product::find($id);
            $products = Product::where('category_id', $product->category_id)->where('id', '!=', $id);
            return new ProductMiniCollection(filter_products($products)->limit(10)->get());
        });
    }

    public function topFromSeller($id)
    {
        return Cache::remember("app.top_from_this_seller_products-$id", 86400, function() use ($id){
            $product = Product::find($id);
            $products = Product::where('user_id', $product->user_id)->orderBy('num_of_sale', 'desc');

            return new ProductMiniCollection(filter_products($products)->limit(10)->get());
        });
    }


    public function search(Request $request)
    {
        $name = $request->query_key;
        
        // Create cache key based on parameters
        $cacheKey = 'app.products_search';
        if ($name) $cacheKey .= '_name_' . md5($name);

        return Cache::remember($cacheKey, 1800, function() use ($name) {
            $products = Product::query();

            $products->where('published', 1);

            // If name is provided, search for exact match only
            if ($name != null && $name != "") {
                $products->where('name', 'like', '%' . $name . '%'); // Partial match
                SearchUtility::store($name);
            }

            // Order by latest
            $products->orderBy('created_at', 'desc');

            // Get the paginated results
            $paginatedProducts = $products->paginate(10);
            
            // Only show "No product found" message when a search term was provided but no results were found
            if ($paginatedProducts->isEmpty() && !empty($name)) {
                // Return empty collection with a custom message
                return response()->json([
                    'data' => [],
                    'message' => 'No product found',
                    'success' => true,
                    'status' => 200
                ]);
            }

            return new ProductMiniCollection($paginatedProducts);
        });
    }

    public function variantPrice(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $str = '';
        $tax = 0;

        if ($request->has('color') && $request->color != "") {
            $str = Color::where('code', '#' . $request->color)->first()->name;
        }

        $var_str = str_replace(',', '-', $request->variants);
        $var_str = str_replace(' ', '', $var_str);

        if ($var_str != "") {
            $temp_str = $str == "" ? $var_str : '-' . $var_str;
            $str .= $temp_str;
        }


        $product_stock = $product->stocks->where('variant', $str)->first();
        $price = $product_stock->price;
        $stockQuantity = $product_stock->qty;


        //discount calculation
        $discount_applicable = false;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }



        return response()->json([
            'product_id' => $product->id,
            'variant' => $str,
            'price' => (double)convert_price($price),
            'price_string' => format_price(convert_price($price)),
            'stock' => intval($stockQuantity),
            'image' => $product_stock->image == null ? "" : api_asset($product_stock->image)
        ]);
    }

    public function home(Request $request)
    {
        // Determine language
        $lang = $request->get('lang', app()->getLocale());
        
        // Cache key includes language
        $cacheKey = "app.products_home_{$lang}";
        
        // Apply sorting based on the request parameter
        $sort = $request->get('sort', 'newest');
        $cacheKey .= "_{$sort}";
        
        return Cache::remember($cacheKey, 86400, function() use ($sort, $lang) {
            $products = Product::query();
            
            switch ($sort) {
                case 'oldest':
                    $products->oldest();
                    break;
                case 'price_low_to_high':
                    $products->orderBy('unit_price', 'asc');
                    break;
                case 'price_high_to_low':
                    $products->orderBy('unit_price', 'desc');
                    break;
                case 'newest':
                default:
                    $products->latest();
                    break;
            }
            
            return new ProductCollection($products->paginate(20));
        });
    }
}