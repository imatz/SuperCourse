{include file="addons/my_product_packages/components/package_filters.tpl" dispatch="products.manage"}


{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{if !$config.tweaks.disable_dhtml}
    {assign var="ajax_class" value="cm-ajax"}
{/if}

{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

<span class="my_packages_new">
{include file="buttons/button.tpl" but_text=__("new_package") but_href="products.add" but_meta="ty-btn__primary"}
</span>
{include file="common/pagination.tpl"}
<form id='form' action="{""|fn_url}" method="post" name="product_update_form" >
<table class="ty-table ty-orders-search ty-packages">
    <thead>
        <tr>
			<th width="8%">{__("code")}</th>
			<th width="40%">{__("name")}</th>
			<th width="5%">{__("type")}</th>
			<th width="8%" class="right">{__("status")}</th>
			{if "Y"==$show_package_retail_price}<th width="8%">{__("retail_price")}</th>{/if}	
			<th width="11%">{__("my_price")}</th>	
			<th width="11%">{__("shop_retail_price")}</th>	
			<th width="8%">&nbsp;</th>
		</tr>
    </thead>

{foreach from=$products item=product}
<form action="{""|fn_url}" method="POST" >
<input type="hidden" name="product_id" value="{$product.product_id}">
<tr class="cm-row-status-{$product.status|lower}" id="row-{$product.product_id}">
    <td class="ty-orders-search__item">{$product.product_code}<br>
	<em class="ty-date">{$product.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</em></td>
    <td class="ty-orders-search__item">{$product.product}<br>{include file="addons/my_product_packages/components/package_products.tpl" package_products=$product.package_products}</td>
	<td class="ty-orders-search__item">{$product.creation}</td>
	<td class="ty-orders-search__item">{include file="addons/my_product_packages/common/select_status.tpl" display="select"  input_name="product_data[status]" obj=$product}</td>
	{if "Y"==$show_package_retail_price}<td class="ty-orders-search__item">{include file="common/price.tpl" value=$product.retail_data_no_discounts.base_taxed_price}</td>{/if}
	<td class="ty-orders-search__item">{include file="common/price.tpl" value=$product.no_package_discount_data.taxed_price} / {include file="common/price.tpl" value=$product.taxed_price}{if "A"==$auth.act_as_user}{include file="addons/my_product_packages/components/package_discount.tpl" discount="b" product=$product}{/if}</td>
	<td class="ty-orders-search__item">{include file="common/price.tpl" value=$product.no_package_discount_data.retail_data.taxed_price} / {include file="common/price.tpl" value=$product.retail_data.taxed_price}{if $auth.act_as_user}{include file="addons/my_product_packages/components/package_discount.tpl" discount="c" product=$product}{/if}</td>
	<td class="ty-orders-search__item">
		{include file="buttons/button.tpl" but_text=__("save") but_name="dispatch[products.update]" but_role="submit" but_meta="ty-btn__primary"}
		<br><br>
        {if $product.status != A && $product.creation != Q}
        {include file="buttons/button.tpl" but_text=__("delete") but_href="products.delete?product_id=`$product.product_id`"}
        {/if}
        <br>
         {*
        {if $product.status != D}
        {include file="buttons/button.tpl" but_text=__("print_code") but_href="products.print?product_id=`$product.product_id`" but_meta="cm-new-window ty-btn__secondary"} 
        {/if}
        *}
                
{***************************************** popup prin tin ektypwsi ***********************************}
{if $product.status != D}
<div class="ty-account-info__buttons buttons-container">
          <a href="{$config.current_url|fn_url}" data-ca-target-id="{$product.product_id}" class="cm-dialog-opener cm-dialog-auto-size ty-btn ty-btn__secondary" rel="nofollow">{__("print_code")}</a>
       		<div id="{$product.product_id}" class="hidden" title="{__("print_code")}">
            	<div class="ty-login-popup">
                	{include file="addons/my_changes/views/my_changes/my_print.tpl" style="popup" id="popup`$block1.snapping_id`"}
                </div>
           	</div>            
        </div>
    </div>
</div>
{/if}
{***************************************** popup prin tin ektypwsi ***********************************}        {*
        <br><br>
        <span class="my_packages_new">
			{include file="buttons/continue.tpl"  but_text = "Αντιγραφή" but_name="dispatch[my_disabled_packages.disabled]" but_role="submit"}
		</span>
        *}
        {*
        <div class="ty-mt-m">
            {include file="buttons/button.tpl" but_id="opener_picker_`$data_id`" but_href="products.package_picker?display=`$display`&picker_for=`$picker_for`&extra=`$extra_var`&checkbox_name=`$checkbox_name`&aoc=`$aoc`&data_id=`$data_id`" but_text=$but_text|default:__("add_products") but_role=$but_role|default:"add" but_target_id="content_`$data_id`" but_meta="ty-btn__secondary cm-dialog-opener cm-dialog-auto-width" but_rel="nofollow" but_icon="product-picker-icon ty-icon-plus"}
    </div></td>
    *}
</tr>
</form>
    {foreachelse}
        <tr class="ty-table__no-items">
            <td colspan="7"><p class="ty-no-items">{__("text_no_packages")}</p></td>
        </tr>
    {/foreach}
</table>

{include file="common/pagination.tpl"}

{capture name="mainbox_title"}{__("my_packages")}{/capture}