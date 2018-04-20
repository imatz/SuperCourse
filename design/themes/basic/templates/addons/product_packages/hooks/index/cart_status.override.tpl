{assign var="_cart_products" value=$smarty.session.cart.products|array_reverse:true}
{foreach from=$_cart_products key="key" item="p" name="cart_products"}
{if !$p.extra.parent && !$p.extra.package_info}
<tr class="minicart-separator">
    {if $block.properties.products_links_type == "thumb"}
    <td style="width: 5%" class="cm-cart-item-thumb">{include file="common/image.tpl" image_width="40" image_height="40" images=$p.main_pair no_ids=true}</td>
    {/if}
    <td style="width: 94%"><a href="{"products.view?product_id=`$p.product_id`"|fn_url}">{$p.product_id|fn_get_product_name nofilter}</a>
    <p>
        <span>{$p.amount}</span><span>&nbsp;x&nbsp;</span>{include file="common/price.tpl" value=$p.display_price span_id="price_`$key`_`$dropdown_id`" class="none"}
    </p></td>
    {if $block.properties.display_delete_icons == "Y"}
    <td style="width: 1%" class="minicart-tools cm-cart-item-delete">{if (!$runtime.checkout || $force_items_deletion) && !$p.extra.exclude_from_calculate}{include file="buttons/button.tpl" but_href="checkout.delete.from_status?cart_id=`$key`&redirect_url=`$r_url`" but_meta="cm-ajax cm-ajax-full-render" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}{/if}</td>
    {/if}
</tr>
{/if}
{/foreach}