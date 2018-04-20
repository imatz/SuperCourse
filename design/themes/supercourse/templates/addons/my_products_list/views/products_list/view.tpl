<div id="content_products_list">
	<h3>1. {__("download_product_list")}</h3>
	<div class="info_text">{__("text_download_product_list")}</div>
	
	{include file="buttons/button.tpl" but_text=__("download_xls") but_href="products_list.xls" but_meta="ty-btn__secondary"}
	{include file="buttons/button.tpl" but_text=__("download_csv") but_href="products_list.csv" but_meta="ty-btn__secondary"}
	
	<h3>2. {__("upload_product_list")}</h3>
	<div class="info_text">{__("text_upload_product_list")}</div>
	
	<form action="{""|fn_url}" method="post" name="products_list_form" enctype="multipart/form-data">
		<div class="products_list_uploader">
			{include file="common/fileuploader.tpl" var_name="products_list_file"}
		</div>
		{include file="buttons/button.tpl" but_text=__("add_to_cart")  but_role="submit" but_meta="ty-btn__primary cm-te-upload-file" but_name="dispatch[products_list.upload]"}
	</form>
</div>

{capture name="mainbox_title"}{__("products_list")}{/capture}



