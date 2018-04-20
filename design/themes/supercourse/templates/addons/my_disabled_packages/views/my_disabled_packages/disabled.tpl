  
        {** /Item menu section **}

        {assign var="categories_company_id" value=$product_data.company_id}
        
        {if $product_data.product_id}
            {assign var="id" value=$product_data.product_id}
        {else}
            {assign var="id" value=0}
        {/if}
        <div style="margin-left: 20px;">
        <form id='form' action="{""|fn_url}" method="post" name="product_update_form" class="form-horizontal form-edit" enctype="multipart/form-data">   <input type="hidden" name="fake" value="1" />
            <input type="hidden" name="product_id" value="{$id}" />

            <div class="product-manage" id="content_detailed"> {* content detailed *}





{* nikosgkil emfanisi listas proiontwn poy apoteloyn tin synthesi *}
$products_in_chosen_package:<br>
{foreach from=$products_in_package item="product"}           
{$product}
<br>
{/foreach}
<br>
{$products[2286].package_products[1949].product|@print_r:true}
<br><br><br>
{*$products|@print_r:true*}
{$foo = $products[2286].package_products[1949].product}
<br>
<br>
{$foo|@print_r:true}
<br>
<br>

{* nikosgkil emfanisi listas proiontwn poy apoteloyn tin synthesi *}





                {include file="common/subheader.tpl" title=__("information") target="#acc_information"}
                <div id="acc_information">
					<input type="hidden" name="product_data[package_data][creation]" value="S">
                    <div class="control-group">
                        <label for="product_description_product" class="control-label cm-required">{__("name")}</label>
                        <div class="controls">
                            <input class="input-large" form="form" type="text" name="product_data[product]" id="product_description_product" size="55" value="{$product_data.product}" />
                        </div>
                    </div>
					
					<div class="control-group">
                        <label for="product_status" class="control-label cm-required">{__("status")}</label>
                        <div class="controls">
                           {include file="addons/my_product_packages/common/select_status.tpl" input_name="product_data[status]" id="elm_product_status" obj=$product_data display="select"}
                        </div>
                    </div>
                </div>
                <hr>
                
                
                
                
                
                {* nikosgkil emfanisi listas proiontwn poy apoteloyn tin synthesi *}
                
                {foreach from=$products_in_package_array item="products_array" key="a"}
                	{$products_array.products_in_package_array[0]}
                    <br>
					{$products_array.products_in_package_array[1]}
                    <br>
                    {$products_array.products_in_package_array|@print_r:true}
                    
                    {$product_data.package_products = $products_array}
                {/foreach}
                {$product_data.package_products|@print_r:true}
                
                {* nikosgkil emfanisi listas proiontwn poy apoteloyn tin synthesi *}
                
                
                
                
                
                
				{include file="common/subheader.tpl" title=__("package_products") target="#pp_picker"}
				<div id="pp_picker">
					{include file="addons/my_disabled_packages/pickers/package_products/picker.tpl" item_ids=$product_data.package_products data_id="package_products" input_name="product_data[package_products]" no_item_text=__("text_no_items_defined", ["[items]" => __("products")]) aoc=true type="table"  extra_mode="package_info"}
				</div>
				<hr>
            </div> 
	{include file="buttons/button.tpl" but_text=__("save_package") but_name="dispatch[products.update]" but_role="submit" but_meta="ty-btn__primary"}
	{include file="buttons/button.tpl" but_text=__("cancel") but_href="products.manage" but_role="" but_meta="ty-btn__secondary"}
		</form>
        </div>
{if $id}
    {capture name="mainbox_title"}
        {"{__("editing_product")}: `$product_data.product`"|strip_tags}
    {/capture}
{else}
    {capture name="mainbox_title"}
        {__("new_package")}
    {/capture}
{/if}