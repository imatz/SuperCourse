
<div id="content_products_list">
	<form action="{""|fn_url}" method="post" name="forms_form" enctype="multipart/form-data">
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
					<td>{if !$pl.error}<input type="hidden" name="product_list[{$no}][A]" value="{$pl.A}">{/if}{$pl.A}</td>
					<td>{$pl.product}</td>
					<td>{if !$pl.error}<input type="hidden" name="product_list[{$no}][B]" value="{$pl.B}">{/if}{$pl.B}</td>
					<td>{$pl.error}</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="4">{__('product_list_no_products')}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>		
		
		{include file="buttons/button.tpl" but_text=__("product_list_confirm")  but_role="submit" but_meta="ty-btn__primary" but_name="dispatch[products_list.confirm]"}
	</form>
</div>
{capture name="mainbox_title"}{__("confirm_product_list")}{/capture}