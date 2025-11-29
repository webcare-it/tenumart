<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Review;
use App\Models\Attribute;

class ProductDetailCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $precision = 2;
                $calculable_price = home_discounted_base_price($data, false);
                $calculable_price = number_format($calculable_price, $precision, '.', '');
                $calculable_price = floatval($calculable_price);

                // --- Photos ---
                $photo_paths = get_images_path($data->photos);
                $photos = [];
                if (!empty($photo_paths)) {
                    foreach ($photo_paths as $path) {
                        if (!empty($path)) {
                            $photos[] = [
                                'variant' => "",
                                'path' => $path,
                            ];
                        }
                    }
                }

                foreach ($data->stocks as $stockItem) {
                    if (!empty($stockItem->image)) {
                        $photos[] = [
                            'variant' => $stockItem->variant,
                            'path' => api_asset($stockItem->image),
                        ];
                    }
                }

                // --- Brand ---
                $brand = [
                    'id' => 0,
                    'name' => "",
                    'logo' => "",
                ];

                if ($data->brand) {
                    $brand = [
                        'id' => $data->brand->id,
                        'name' => $data->brand->name,
                        'logo' => api_asset($data->brand->logo),
                    ];
                }

                // --- Product name & description ---
                $translatedName = $data->name;
                $translatedDescription = $data->description;

                return [
                    'id' => (integer)$data->id,
                    'name' => $translatedName,
                    'slug' => $data->slug,
                    'added_by' => $data->added_by,
                    'category_name' => $data->category->name,
                    'seller_id' => $data->user->id ?? "",
                    'shop_id' => $data->added_by == 'admin' ? 0 : $data->user->shop->id,
                    'shop_name' => $data->added_by == 'admin' ? 'In House Product' : $data->user->shop->name,
                    'shop_logo' => $data->added_by == 'admin' ? api_asset(get_setting('header_logo')) : api_asset($data->user->shop->logo),
                    'photos' => $photos,
                    'thumbnail_image' => api_asset($data->thumbnail_img),
                    'tags' => explode(',', $data->tags),
                    'price_high_low' => (double)explode('-', home_discounted_base_price($data, false))[0] == (double)explode('-', home_discounted_price($data, false))[1]
                        ? format_price((double)explode('-', home_discounted_price($data, false))[0])
                        : "From " . format_price((double)explode('-', home_discounted_price($data, false))[0]) . " to " . format_price((double)explode('-', home_discounted_price($data, false))[1]),
                    'choice_options' => $this->convertToChoiceOptions(json_decode($data->choice_options)),
                    'colors' => json_decode($data->colors),
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false),
                    'stroked_price' => home_base_price($data),
                    'main_price' => home_discounted_base_price($data),
                    'calculable_price' => $calculable_price,
                    'currency_symbol' => currency_symbol(),
                    'current_stock' => (integer)optional($data->stocks->first())->qty,
                    'unit' => $data->unit,
                    'rating' => (double)$data->rating,
                    'rating_count' => (integer)Review::where(['product_id' => $data->id])->count(),
                    'earn_point' => (double)$data->earn_point,
                    'description' => $translatedDescription,
                    'video_provider' => $data->video_provider ?? "",
                    'video_link' => $data->video_link ?? "",
                    'brand' => $brand,
                    'link' => route('product', $data->slug),
                    'variants' => $this->getAllVariantPrices($data),
                ];
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

    // Updated to remove multilingual support
    protected function convertToChoiceOptions($data)
    {
        $result = [];
        if ($data) {
            foreach ($data as $choice) {
                $attribute = Attribute::find($choice->attribute_id);

                $item['name'] = $choice->attribute_id;
                $item['title'] = $attribute->name;
                $item['options'] = $choice->values;
                $result[] = $item;
            }
        }
        return $result;
    }

    protected function getAllVariantPrices($product)
    {
        // (Keep your existing variant price logic)
        $variants = [];
        $color_codes = [];

        if (!empty($product->colors)) {
            $color_codes = \App\Models\Color::whereIn('code', json_decode($product->colors))
                ->get(['name', 'code'])
                ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->code])
                ->toArray();
        }

        foreach ($product->stocks as $stock) {
            $price = $stock->price;
            $stockQuantity = $stock->qty;
            $image = $stock->image ? api_asset($stock->image) : "";
            $variantName = $stock->variant ?? "";

            $colorName = '';
            $sizeName = '';

            if (strpos($variantName, '-') !== false) {
                $parts = explode('-', $variantName);
                $colorName = trim($parts[0]);
                if (isset($parts[1])) $sizeName = trim($parts[1]);
            } else {
                if (isset($color_codes[strtolower($variantName)])) {
                    $colorName = trim($variantName);
                } else {
                    $sizeName = trim($variantName);
                }
            }

            $colorCode = $color_codes[strtolower($colorName)] ?? null;

            $discount_applicable = false;
            $now = strtotime(date('d-m-Y H:i:s'));

            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif ($now >= $product->discount_start_date && $now <= $product->discount_end_date) {
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

            $variants[] = [
                'variant_name' => $variantName,
                'color_name' => $colorName,
                'color_code' => $colorCode,
                'size_name' => $sizeName,
                'variant_price' => (double)convert_price($price),
                'variant_price_string' => format_price(convert_price($price)),
                'variant_stock' => intval($stockQuantity),
                'variant_image' => $image,
            ];
        }

        return $variants;
    }
}