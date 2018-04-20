<table>
{foreach from=$package_info item="item" key="key"}
    <tr>
          <td style="border: none; vertical-align: top;">
                <div class="cm-reload-{$product.product_id}" id="product_package_image_{$product.product_id}_{$key}">
                <a href="{"products.view&amp;product_id=`$item.0.product_id`"|fn_url}">{include file="common/image.tpl" images=$item.0.product_data.main_pair image_height="65"}</a>
                <!--product_package_image_{$product.product_id}_{$key}--></div>
          </td>
          <td style="border: none;" width="15px">
            
          </td>
         
          <td style="border: none;">
                {if $item.0.multiple == "Y" && !$item.0.product_options && $item.0.product_data.product_options}
                    {assign var="amount" value=1}
                    {assign var="loop" value=$item|count}
                {else}
                    {assign var="amount" value=$item.0.amount}
                    {assign var="loop" value=1}
                {/if}
                <div>
                      <a href="{"products.view&amp;product_id=`$item.0.product_id`"|fn_url}">{$item.0.product_data.product}</a>
                </div>
                {if $product.price_rule == "S"}
                <div class="cm-reload-{$product.product_id}" id="product_package_prices_{$product.product_id}_{$key}">
                      {if $item.0.f_price<$item.0.price}<strong class="strike">{include file="common/price.tpl" value=$item.0.price}</strong>{/if} 
                      <strong>{include file="common/price.tpl" value=$item.0.f_price}</strong> x <strong>{$loop*$amount}</strong> 
                      {if $item.0.modifiers_price}&nbsp;+&nbsp;<strong>{include file="common/price.tpl" value=$item.0.modifiers_price}</strong>{/if}
                      = <strong>{include file="common/price.tpl" value=$item.0.f_price*$loop*$amount+$item.0.modifiers_price}</strong>
                <!--product_package_prices_{$product.product_id}_{$key}--></div>
                {else}
                    <div>
                        <strong>{__("amount")}: {$loop*$amount}</strong>
                    </div>
                {/if}
                
                {section name=foo start=0 loop=$loop step=1}
                    <input type="hidden" value="{$item.0.product_id}" name="product_data[{$product.product_id}][package][{$key}][{$smarty.section.foo.index}][product_id]">
                    <input type="hidden" value="{$amount}" name="product_data[{$product.product_id}][package][{$key}][{$smarty.section.foo.index}][amount]">
                    {if $item.0.product_options}
                        <div class="cm-picker-product-options">
                            {include file="common/options_info.tpl" product_options=$item.0.product_data.product_options}
                        </div>
                    {elseif $item.0.product_data.product_options}
                        {assign var="package_key" value=$smarty.section.foo.index}
                        <div class="options-wrapper indented cm-reload-{$product.product_id}" id="product_package_option_{$product.product_id}_{$key}_{$package_key}">
                            {include file="addons/product_packages/components/product_options.tpl" product_options=$item.$package_key.product_data.product_options obj_prefix="pp" show_package_modifiers=$product.price_rules_options id=$key main_object_prefix=$obj_prefix index=$package_key main_id=$product.product_id product=$item.$package_key.product_data name="product_data[{$product.product_id}][package]"}
                        <!--product_package_option_{$product.product_id}_{$key}_{$package_key}--></div>
                    {/if}
                {/section}
          </td>
    </tr>
    <tr height="5px"><td colspan="3">&nbsp;</td></tr>
{/foreach}
{assign var="no_product_items" value=$product.product_id|fn_get_no_products}
{foreach from=$no_product_items item="item" key="key"}
    <tr>
        <td style="border: none; vertical-align: top;">
            &nbsp;
        </td>
        <td style="border: none;" width="15px">
            &nbsp;
        </td>
        
        <td style="border: none;">
            <div>
                    {$item.name}
            </div>
            {if $product.price_rule == "S"}
                <div>
                    <strong>{include file="common/price.tpl" value=$item.price}</strong> x <strong>{$item.amount}</strong> 
                    = <strong>{include file="common/price.tpl" value=$item.price*$item.amount}</strong>
                </div>
            {else}
                <div>
                    <strong>{__("amount")}: {$loop*$amount}</strong>
                </div>
            {/if}
        </td>
    </tr>
    <tr height="5px"><td colspan="3">&nbsp;</td></tr>
{/foreach}
</table>