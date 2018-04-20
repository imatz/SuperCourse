{if !$cart.products.$key.extra.package_info}
    <tr>
        <td class="left">
            <input type="checkbox" name="cart_ids[]" value="{$key}" class="cm-item" /></td>
        <td>
            {if $cp.product_options}
                <span id="on_product_options_{$key}_{$cp.product_id}" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="hand cm-combination-options-{$id}"><span class="exicon-expand"></span></span>
                <span id="off_product_options_{$key}_{$cp.product_id}" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" class="hand hidden cm-combination-options-{$id}"><span class="exicon-collapse"></span> </span>
            {/if}
            <a href="{"products.update?product_id=`$cp.product_id`"|fn_url}">{$cp.product nofilter}</a>
            {include file="views/companies/components/company_name.tpl" object=$cp}
        </td>
        <td width="3%">
            {if $cp.exclude_from_calculate}
                {__("free")}
                {else}
                <input type="hidden" name="cart_products[{$key}][stored_price]" value="N" />
                <input class="inline" type="checkbox" name="cart_products[{$key}][stored_price]" value="Y" {if $cp.stored_price == "Y"}checked="checked"{/if} onchange="Tygh.$('#db_price_{$key},#manual_price_{$key}').toggle();"/>
            {/if}
        </td>
        <td class="left">
        {if !$cp.exclude_from_calculate}
            {if $cp.stored_price == "Y"}
                {math equation="price - modifier" price=$cp.original_price modifier=$cp.modifiers_price|default:0 assign="original_price"}
            {else}
                {assign var="original_price" value=$cp.original_price}
            {/if}
            <span class="{if $cp.stored_price == "Y"}hidden{/if}" id="db_price_{$key}">{include file="common/price.tpl" value=$original_price}</span>
            <div class="{if $cp.stored_price != "Y"}hidden{/if}" id="manual_price_{$key}">
                {include file="common/price.tpl" value=$cp.base_price view="input" input_name="cart_products[`$key`][price]" class="input-hidden input-mini" }
            </div>
        {/if}
        </td>
        {if $cart.use_discount}
        <td class="no-padding nowrap">
        {if $cp.exclude_from_calculate}
            {include file="common/price.tpl" value=""}
        {else}
            {if $cart.order_id}
            <input type="hidden" name="cart_products[{$key}][stored_discount]" value="Y" />
            <input type="text" class="input-hidden input-mini cm-numeric" size="5" name="cart_products[{$key}][discount]" value="{$cp.discount}" data-a-sign="{$currencies.$primary_currency.symbol|strip_tags nofilter}" data-a-dec="," data-a-sep="." />
            {else}
            {include file="common/price.tpl" value=$cp.discount}
            {/if}
        {/if}
        </td>
        {/if}
        <td class="center">
            <input type="hidden" name="cart_products[{$key}][product_id]" value="{$cp.product_id}" />
            {if $cp.exclude_from_calculate}
            <input type="hidden" size="3" name="cart_products[{$key}][amount]" value="{$cp.amount}" />
            {/if}
            <span class="cm-reload-{$key}" id="amount_update_{$key}">
                <input class="input-hidden input-micro" type="text" size="3" name="cart_products[{$key}][amount]" value="{$cp.amount}" {if $cp.exclude_from_calculate}disabled="disabled"{/if} />
            <!--amount_update_{$key}--></span>
        </td>
        <td class="nowrap">
            <div class="hidden-tools">
                <a class="cm-confirm icon-trash" href="{"order_management.delete?cart_id=`$key`"|fn_url}" title="{__("delete")}"></a>
            </div>
        </td>
    </tr>
    {if $cp.product_options}
    <tr id="product_options_{$key}_{$cp.product_id}" class="cm-ex-op hidden row-more row-gray">
        <td>&nbsp;</td>
        <td colspan="{if $cart.use_discount}6{else}5{/if}">
            {include file="views/products/components/select_product_options.tpl" product_options=$cp.product_options name="cart_products" id=$key use_exceptions="Y" product=$cp additional_class="option-item"}
            <div id="warning_{$key}" class="pull-left notification-title-e hidden">&nbsp;&nbsp;&nbsp;{__("nocombination")}</div>
        </td>
    </tr>
    {/if}
{else}
    <span class="hidden">&nbsp;</span>
{/if}
{if $cart.products.$key.extra.package_hash}
    <tr class="cm-ex-op row-more row-gray">
        <td>&nbsp;</td>
        <td colspan="{if $cart.use_discount}6{else}5{/if}">
            {include file="common/subheader.tpl" title=__("package_info") target="#product_package_`$key`"}
            <div id="product_package_{$key}">
                {foreach from=$cart_products item="pp" key="pk"}
                    {if $cart.products.$pk.extra.package_info.p_id == $key}
                        <div>
                            {$pp.product}
                            {assign var="opt_name" value="cart_products[`$key`][package]"}
                            {assign var="show_options" value=$cp.product_id|fn_show_package_product_options:$cart.products.$pk.extra.package_info.table_key}
                            {if !$show_options}
                                {include file="addons/product_packages/components/select_product_options.tpl" product_options=$pp.product_options name=$opt_name main_id=$key id="`$cart.products.$pk.extra.package_info.table_key`" index="`$cart.products.$pk.extra.package_info.inc`" use_exceptions="Y" product=$pp additional_class="option-item"}
                            {else}
                                <div>
                                {assign var="options" value=$pp.product_options|fn_prepare_package_product_options:$show_options}
                                {include file="common/options_info.tpl" product_options=$options inline_option=false}
                                </div>
                            {/if}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </td>
    </tr>
{/if}
