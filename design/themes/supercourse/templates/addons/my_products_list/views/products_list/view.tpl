<div id="content_products_list">
	<div class="info_text">{__("text_upload_product_list")}</div>
	
	{include file="buttons/button.tpl" but_text=__("download_xls_demo") but_href="products_list.xls_demo" but_meta="ty-btn__text"}
	{include file="buttons/button.tpl" but_text=__("download_csv_demo") but_href="products_list.csv_demo" but_meta="ty-btn__text"}
		
	<form action="{""|fn_url}" method="post" name="forms_form" enctype="multipart/form-data">
		<div class="products_list_uploader">
			{include file="common/fileuploader.tpl" var_name="products_list_file[0]"}
			
		</div>
		
		{include file="buttons/button.tpl" but_text=__("add_to_cart")  but_role="submit" but_meta="ty-btn__primary" but_name="dispatch[products_list.upload]"}
		
	<hr>	
	<h3>{__("manual_product_list")}</h3>
	<div class="info_text">{__("text_manual_product_list")}</div>
		{*include file="buttons/button.tpl" but_text=__("products_list_add_product") but_meta="ty-btn__secondary add-btn"*}
		<table id="products_list_tbl">
			<thead>
				<tr>
					<th width="45%">{__("code")}</th>
					<th width="25%">{__("quantity")}</th>
					<th width="30%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td><input type="text" name="product_list[0][A]"></td>
				<td><input type="text" size="5" class="cm-value-integer" name="product_list[0][B]"</td>
				<td><a class="ty-btn ty-btn__primary add-btn">+</a></td>
			</tr>
			</tbody>
		</table>		
		
		{include file="buttons/button.tpl" but_text=__("add_to_cart")  but_role="submit" but_meta="ty-btn__primary" but_name="dispatch[products_list.manual]"}
	</form>
</div>
{literal}
	<script>
		$(document).ready(function(){
			var products_list_tbl_cnt = $('#products_list_tbl tbody tr').length;
			$('#products_list_tbl').on('click', '.del-btn', function(){$(this).parent().parent().remove()})
			                       .on('click', '.add-btn',function(){
				$('<tr><td><input type="text" name="product_list['+ products_list_tbl_cnt +'][A]"></td><td><input type="text"  size="5" class="cm-value-integer" name="product_list['+ products_list_tbl_cnt +'][B]"></td><td><a class="ty-btn ty-btn__primary add-btn">+</a><a class="ty-btn ty-btn__secondary del-btn">-</a></td></tr>').insertAfter($(this).parent().parent());
				products_list_tbl_cnt++;
			});
		});
	</script>
{/literal}
{capture name="mainbox_title"}{__("upload_product_list")}{/capture}



