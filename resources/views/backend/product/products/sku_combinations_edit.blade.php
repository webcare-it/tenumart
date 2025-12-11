@if(isset($product) && $product->b_product_id != null && $product->stocks->count() > 0)
    {{-- For Droploo products with existing stocks - WITHOUT wholesale price column --}}
    <table class="table table-bordered aiz-table">
        <thead>
            <tr>
                <td class="text-center">
                    {{translate('Variant')}}
                </td>
                <td class="text-center">
                    {{translate('Variant Price')}}
                </td>
                <td class="text-center" data-breakpoints="lg">
                    {{translate('SKU')}}
                </td>
                <td class="text-center" data-breakpoints="lg">
                    {{translate('Quantity')}}
                </td>
                <td class="text-center" data-breakpoints="lg">
                    {{translate('Photo')}}
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach ($product->stocks as $stock)
                @php
                    $str = $stock->variant;
                    $field_str = str_replace('.', '_', $str);
                @endphp
                <tr class="variant">
                    <td>
                        <label for="" class="control-label">{{ $str }}</label>
                    </td>
                    <td>
                        <input type="number" lang="en" name="price_{{ $field_str }}" value="{{ $stock->price }}" min="0" step="0.01" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="sku_{{ $field_str }}" value="{{ $stock->sku }}" class="form-control">
                    </td>
                    <td>
                        <input type="number" lang="en" name="qty_{{ $field_str }}" value="{{ $stock->qty }}" min="0" step="1" class="form-control" required>
                    </td>
                    <td>
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                            </div>
                            <div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="img_{{ $field_str }}" class="selected-files" value="{{ $stock->image }}">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif(isset($combinations) && count($combinations) > 0 && count($combinations[0]) > 0)
    {{-- For regular products with combinations --}}
    <table class="table table-bordered aiz-table">
        <thead>
            <tr>
                <td class="text-center">
                    {{translate('Variant')}}
                </td>
                <td class="text-center">
                    {{translate('Variant Price')}}
                </td>
                <td class="text-center" data-breakpoints="lg">
                    {{translate('SKU')}}
                </td>
                <td class="text-center" data-breakpoints="lg">
                    {{translate('Quantity')}}
                </td>
                <td class="text-center" data-breakpoints="lg">
                    {{translate('Photo')}}
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach ($combinations as $key => $combination)
                @php
                    $variation_available = false;
                    $sku = '';
                    foreach (explode(' ', $product_name) as $key => $value) {
                        $sku .= substr($value, 0, 1);
                    }

                    $str = '';
                    foreach ($combination as $key => $item){
                        if($key > 0 ) {
                            $str .= '-'.str_replace(' ', '', $item);
                            $sku .='-'.str_replace(' ', '', $item);
                        }
                        else {
                            if($colors_active == 1) {
                                $color_name = \App\Models\Color::where('code', $item)->first()->name;
                                $str .= $color_name;
                                $sku .='-'.$color_name;
                            }
                            else {
                                $str .= str_replace(' ', '', $item);
                                $sku .='-'.str_replace(' ', '', $item);
                            }
                        }
                        $stock = $product->stocks->where('variant', $str)->first();
                    }
                    
                    // FIX: Ensure the string is properly formatted for field names (replace dots, hyphens, spaces with underscores)
                    $field_str = str_replace('.', '_', $str);
                    $field_str = str_replace('-', '_', $field_str);
                    $field_str = str_replace(' ', '_', $field_str);
                @endphp
                @if(strlen($str) > 0)
                <tr class="variant">
                    <td>
                        <label for="" class="control-label">{{ $str }}</label>
                    </td>
                    <td>
                        <input type="number" lang="en" name="price_{{ $field_str }}" value="@php
                                if ($product->unit_price == $unit_price) {
                                    if($stock != null){
                                        echo $stock->price;
                                    }
                                    else {
                                        echo $unit_price;
                                    }
                                }
                                else{
                                    echo $unit_price;
                                }
                               @endphp" min="0" step="0.01" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="sku_{{ $field_str }}" value="@php
                                if($stock != null) {
                                    echo $stock->sku;
                                }
                                else {
                                    echo $str;
                                }
                               @endphp" class="form-control">
                    </td>
                    <td>
                        <input type="number" lang="en" name="qty_{{ $field_str }}" value="@php
                                if($stock != null){
                                    echo $stock->qty;
                                }
                                else{
                                    echo '10';
                                }
                               @endphp" min="0" step="1" class="form-control" required>
                    </td>
                    <td>
                        <div class=" input-group " data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                            </div>
                            <div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="img_{{ $field_str }}" class="selected-files" value="@php
                                    if($stock != null){
                                        echo $stock->image;
                                    }
                                    else{
                                        echo null;
                                    }
                                   @endphp">
                        </div>
                        <div class="file-preview box sm"></div>
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endif