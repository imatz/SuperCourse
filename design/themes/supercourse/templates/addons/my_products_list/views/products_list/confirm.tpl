
<div id="content_products_list">
	<form action="{""|fn_url}" method="POST" name="forms_form">
		<table class="ty-table" id="products_list_confirm_tbl">
			<thead>
				<tr>
					<th>{__("code")}</th>
					<th>{__("product")}</th>
					<th>{__("quantity")}</th>
					<th>{__("error")}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$product_list item=pl key=no}
				<tr>
					<td><input type="hidden" name="{if !$pl.error}product_list{else}error_list{/if}[{$no}][A]" value="{$pl.A}">{$pl.A}</td>
					<td>{$pl.product}</td>
					<td><input type="hidden" name="{if !$pl.error}product_list{else}error_list{/if}[{$no}][B]" value="{$pl.B}">{$pl.B}</td>
					<td>{$pl.error}</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="4">{__('product_list_no_products')}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>		
		
		{include file="buttons/button.tpl" but_text=__("confirm_product_list")  but_role="submit" but_meta="ty-btn__primary" but_name="dispatch[products_list.confirm]"}
		{include file="buttons/button.tpl" but_text=__("print_product_list_errors")  but_role="submit" but_meta="ty-btn__secondary ty-float-right" but_name="dispatch[products_list.print_err]"}
	</form>
</div>
{capture name="mainbox_title"}{__("confirm_product_list")}{/capture}