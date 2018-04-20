{if ($extra_mode == "package_info") && $product_info}
    {if "ULTIMATE"|fn_allowed_for && $product_infok.product_id && $runtime.company_id}
        {assign var="product_data" value=$product_info.product_id|fn_get_product_data:$smarty.session.auth:$smarty.const.CART_LANGUAGE:"?:products.company_id,?:product_descriptions.product":false:false:false:false:false:false:true}
        {if $product_data.company_id != $runtime.company_id}
            {assign var="product" value=$product_data.product|default:$product}
            {if $owner_company_id && $owner_company_id != $runtime.company_id}
                {assign var="show_only_name" value=true}
            {/if}
        {/if}
    {/if}
    <td>
         <input type="text" name="{$input_name}[position]" id="item_position_{$delete_id}" size="4" value="{$product_info.position|default:0}" class="input-micro">
    </td>
    <td>
        {if !$show_only_name}<a href="{"products.update?product_id=`$product_info.product_id`"|fn_url}">{__("edit")}</a>{/if}
    </td>
    <td>
        <input type="hidden" id="item_price_pp_{$delete_id}" class="pp_price" value="{$product_info.price|default:0}" name="{$input_name}[price]"/>
        {include file="common/price.tpl" value=$product_info.price}
    </td>
    <td>
        <select name="{$input_name}[p_modifier_type]" class="input-slarge pp_modifier_type" id="item_modifier_type_pp_{$delete_id}">
            <option value="by_fixed" {if $product_info.p_modifier_type == "by_fixed"}selected="selected"{/if}>{__("by_fixed")}</option>
            <option value="to_fixed" {if $product_info.p_modifier_type == "to_fixed"}selected="selected"{/if}>{__("to_fixed")}</option>
            <option value="by_percentage" {if $product_info.p_modifier_type == "by_percentage"}selected="selected"{/if}>{__("by_percentage")}</option>
            <option value="to_percentage" {if $product_info.p_modifier_type == "to_percentage"}selected="selected"{/if}>{__("to_percentage")}</option>
        </select>
    </td>
    <td>
        <input type="hidden"  value="{$delete_id}" />
        <input type="text" name="{$input_name}[p_modifier]" id="item_modifier_pp_{$delete_id}" size="4" value="{$product_info.p_modifier|default:0}" class="input-mini pp_modifier">
    </td>
    <td>
        <input type="hidden" id="item_f_price_pp_{$delete_id}" class="pp_f_price" value="{$product_info.f_price|default:0}" name="{$input_name}[f_price]"/>
        {include file="common/price.tpl" value=$product_info.discounted_price span_id="item_discounted_price_pp_`$delete_id`" class="pp_discounted_price"}
    </td>  
    
    <td>
        <input type="hidden" id="item_f_multi_pp_n_{$delete_id}" class="" value="N" name="{$input_name}[multiple]"/>
        <input type="checkbox" id="item_f_multi_pp_{$delete_id}" class="" value="Y" name="{$input_name}[multiple]" {if $product_info.multiple=="Y"}checked{/if}/>
    </td>  
    
{elseif $extra_mode == "package_info" && $clone}
    <td>
         <input type="text" name="{$input_name}[position]" id="item_position_pp_{$item.chain_id}_{$ldelim}pp_id{$rdelim}" size="4" value="0" class="input-micro">
    </td>
    <td>
    
    </td>
    <td>
        <input type="text" name="{$input_name}[price]" class="hidden pp_price" id="item_price_pp_{$ldelim}pp_id{$rdelim}" value="{$ldelim}price{$rdelim}">
        {include file="common/price.tpl" span_id="item_display_price_pp_`$ldelim`pp_id`$rdelim`"}
    </td>
    <td>
        <select name="{$input_name}[p_modifier_type]" class="input-slarge pp_modifier_type" id="item_modifier_type_pp_{$ldelim}pp_id{$rdelim}">
            <option value="by_fixed">{__("by_fixed")}</option>
            <option value="to_fixed">{__("to_fixed")}</option>
            <option value="by_percentage">{__("by_percentage")}</option>
            <option value="to_percentage">{__("to_percentage")}</option>
        </select>
    </td>
    <td>
        <input type="text" class="hidden" value="{$ldelim}pp_id{$rdelim}" />
        <input type="text" class="hidden" id="{$ldelim}pp_id{$rdelim}" value="0" />
        <input type="text" name="{$input_name}[p_modifier]" id="item_modifier_pp_{$item.chain_id}_{$ldelim}pp_id{$rdelim}" size="4" value="0" class="input-mini pp_modifier">
    </td>
    <td>
        <input type="hidden" id="item_f_price_pp_{$delete_id}" class="pp_f_price" value="" name="{$input_name}[f_price]"/>
        {include file="common/price.tpl" span_id="item_discounted_price_pp_`$ldelim`pp_id`$rdelim`" class="pp_discounted_price"}
    </td>
    <td>
        <input type="hidden" id="item_f_multi_pp_n_{$delete_id}" class="" value="N" name="{$input_name}[multiple]"/>
        <input type="checkbox" id="item_f_multi_pp_{$delete_id}" class="" value="Y" name="{$input_name}[multiple]" />
    </td>
{/if}
    
