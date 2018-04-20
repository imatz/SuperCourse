{if !$cart.products.$key.extra.parent && !$cart.products.$key.extra.package_info}
 <li>
    <a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-name">{$product.product nofilter}</a>{if !$product.exclude_from_calculate}{include file="buttons/button.tpl" but_href="checkout.delete?cart_id=`$key`&redirect_mode=`$runtime.mode`" but_meta="delete" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}{/if}
    <span class="product-price">{$product.amount}&nbsp;x&nbsp;{include file="common/price.tpl" value=$product.display_price}</span>
    {include file="common/options_info.tpl" product_options=$product.product_options no_block=true}
    {hook name="block_checkout:product_extra"}{/hook}
</li>
{else}
<span class="hidden">aa</span>
{/if}