<h3>{!! trans('titles.cart_dialog') !!}</h3>
@if (app('cart')->getContent()->count())
<table class="table">
    <tbody>

        @foreach (app('cart')->getContent() as $product)

        <tr class="cart-product-row">

            <td class="image">

                <a href="/{{ $product['attributes']['product_category_slug'] }}/{{ $product['id'] }}/{{ $product['attributes']['slug'] }}" title="terug naar product">   

                    @if(isset($product['attributes']['attributeIds']) AND ProductHelper::getImage($product['attributes']['id'], $product['attributes']['attributeIds'])) 
                    <img src="/files/product/100x100/{!! $product['attributes']['id'] !!}/{!! ProductHelper::getImage($product['attributes']['id'], $product['attributes']['attributeIds']) !!}" alt="">
                    @else
                    <img src="/images/product-thumb2.jpg" />
                    @endif                                          
                                            
                </a>
            </td>
            <td>

                {!! $product['attributes']['title'] !!}
                @if(isset($product['attributes']['product_combination_title']))
                <ul>
                    @foreach($product['attributes']['product_combination_title'] as $title => $value)
                    <li>{!! $title !!}: {!! $value !!}</li>
                    @endforeach
                </ul>
                @endif                    

            </td>
            <td class="price">
                &euro; <span class="total_price_inc_tax_{{ $product['attributes']['id'] }}">{!! $product->getOriginalPriceWithTaxSum() !!}</span></li>
             
            </td>
        </tr>

        @endforeach 

        <tr class="total">
            <td colspan="3"><strong>{!! trans('titles.total') !!}: &euro; {!! app('cart')->getSubTotalWithTax() !!}</strong></td>
        </tr>

    </tbody>    
</table>
<a href="{!! URL::route('cart.index') !!}" class="btn btn-info">Show shoppingcart</a>                           
<a href="{!! URL::route('cart.index') !!}" class="btn btn-success">Order now</a>
@else 
<p>{!! trans('text.cart-no-items') !!}</p>
@endif