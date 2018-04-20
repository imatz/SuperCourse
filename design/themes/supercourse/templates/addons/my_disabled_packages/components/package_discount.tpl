<br>
<em>{__("discount")} {$discount|upper}:</em>
{assign var=dtype value="`$discount`_discount_type"}
{assign var=dvalue value="`$discount`_discount_value"}
<select class="" name="product_data[package_data][{$dtype}]">
	<option value="" >---</option>
	<option value="A" {if $product.$dtype == "A"}selected="selected"{/if}>{__("absolute")} ({$currencies.$primary_currency.symbol nofilter})</option>
	<option value="P" {if $product.$dtype == "P"}selected="selected"{/if}>{__("percent")} (%)</option>
</select>
<br><br>
<input type="text" name="product_data[package_data][{$dvalue}]" value="{$product.$dvalue}">