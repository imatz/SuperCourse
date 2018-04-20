{include file="common/subheader.tpl" title=__("product_packages") target="#acc_product_packages"}
<div id="acc_product_packages" class="collapse in">
    <div class="control-group">
        <label class="control-label" for="product_packages">{__("product_package")}:</label>
        <div class="controls">
            <input type="hidden" name="product_data[package]" value="N" />
            <input type="checkbox" name="product_data[package]" id="product_packages" value="Y" {if $product_data.package == "Y"}checked="checked"{/if} class="checkbox" />
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="price_rules">{__("price_rules")}:</label>
        <div class="controls">
            <select name="product_data[price_rule]" id="price_rules">
               <option {if $product_data.price_rule == 'F'}selected="selected"{/if} value="F">{__("price_field_rul")}</option>       
               <option {if $product_data.price_rule == 'S'}selected="selected"{/if} value="S">{__("summ_rule")}</option>
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="price_rules_options">{__("price_rules_options")}:</label>
        <div class="controls">
	    <input type="hidden" name="product_data[price_rules_options]" value="N" />
	    <input type="checkbox" name="product_data[price_rules_options]" id="product_packages" value="Y" {if $product_data.price_rules_options == "Y"}checked="checked"{/if} class="checkbox" />
        </div>
    </div>
    
    <div class="control-group">
        <label class="control-label" for="price_rules">{__("package_form")}:</label>
        <div class="controls">
	    <input type="hidden" name="product_data[package_form]" value="N" />
	    <input type="checkbox" name="product_data[package_form]" id="product_packages" value="Y" {if $product_data.package_form == "Y"}checked="checked"{/if} class="checkbox" />
        </div>
    </div>
</div>