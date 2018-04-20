<div id="content_package_info" class="hidden">

{include file="common/subheader.tpl" title=__("package_info") target="#pp_picker"}
<div id="pp_picker">
    
    {include file="pickers/products/picker.tpl" item_ids=$package_info data_id="package_products" input_name="package_info[product_ids]" no_item_text=__("text_no_items_defined", ["[items]" => __("products")]) aoc=true type="table"  extra_mode="package_info" }
    
    <ul class="pull-right unstyled right span6">
    {if $allow_save}
        <li>
            <a class="btn" onclick="fn_pp_recalculate();">{__("recalculate")}</a><br><br>
        </li>
    {/if}
        <li>
            <em>{__("pp_total_price")}:</em>
            <strong>{include file="common/price.tpl" value=$package_info.total_price span_id="total_pp_price"}</strong>
        </li>
        <li>
            <em>{__("pp_total_discount_price")}:</em>
            <strong>{include file="common/price.tpl" value=$package_info.discounted_price span_id="discounted_pp_price"}</strong>
        </li>
        <li>
            <em>{__("pp_total_discount")}:</em>
            <strong>{include file="common/price.tpl" value=($package_info.total_price - $package_info.discounted_price) span_id="discount_pp"}</strong>
        </li>
    {if $allow_save}
        <li><br>
            <label for="elm_pp_global_discount_{$id}"><em>{__("share_discount")}&nbsp;({$currencies.$primary_currency.symbol nofilter}):</em>&nbsp;<input type="text" class="input-mini" size="4" id="elm_pp_global_discount" onkeypress="fn_pp_apply_discount(event);" />&nbsp;<a onclick="fn_pp_apply_discount();" class="btn">{__("apply")}</a></label>
        </li>
    {/if}
    </ul>          
</div>
    
    <script>
    
    Tygh.$(function(){
        $.ceEvent('on','ce.notificationshow',function(data){
            fn_pp_recalculate();
        });
        
        fn_pp_recalculate();
        
        var header_count = $("#pp_picker table th").length;
        $("#pp_picker .no-items td").each(function(ind,ob){
            if($(ob).prop("colspan")){
                $(ob).prop("colspan",header_count);
            }
        });
        
        
    });
    </script>
<div class="clearfix">&nbsp;</div>
<div> 
    {include file="common/subheader.tpl" title=__("no_product_items")}
    <table class="table" width="100%">
        <tr>
            <th width="45%">{__("name")}</th>
            <th width="5%">{__("position")}</th>
            <th width="20%">{__("amount")}</th>
            <th width="20%">{__("price")}</th>
            <th>&nbsp;</th>
        </tr>
        {foreach from=$no_product_items item="i" key="k"}
            <tr>
                <td>
                    <input value="{$i.name}" type="text" class="input-large" name="no_product_items[{$k}][name]">
                </td>
                <td>
                    <input value="{$i.position}" type="text" class="input-micro" name="no_product_items[{$k}][position]">
                </td>
                <td>
                    <input value="{$i.amount}" type="text" class="input-text" name="no_product_items[{$k}][amount]">
                </td>
                <td>
                    <input value="{$i.price}" type="text" class="input-text" name="no_product_items[{$k}][price]">
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
        {/foreach}
        {math equation="x+1" x=$k|default:0 assign="new_key"}
        <tr id="box_add_no_products">
            <td>
                <input  type="text" class="input-large" name="no_product_items[{$new_key}][name]"   value="">
            </td>
            <td>
                <input  type="text" class="input-micro" name="no_product_items[{$new_key}][position]"   value="">
            </td>
            <td>
                <input  type="text" class="input-text" name="no_product_items[{$new_key}][amount]"   value="">
            </td>
            <td>
                <input  type="text" class="input-text" name="no_product_items[{$new_key}][price]"   value="">
            </td>
            <td>
                {include file="buttons/multiple_buttons.tpl" item_id="add_no_products"}
            </td>
        </tr>
    </table>
</div>
<!--content_package_info--></div>