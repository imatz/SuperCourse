
	<span class="cm-reload-{$obj_prefix}{$obj_id} image-reload" id="list_image_update_{$obj_prefix}{$obj_id}">

		{assign var="discount_label" value="discount_label_`$obj_prefix``$obj_id`"}
		{$smarty.capture.$discount_label nofilter}

		<input type="hidden" name="image[list_image_update_{$obj_prefix}{$obj_id}][data]" value="{$obj_id_prefix},{$settings.Thumbnails.product_lists_thumbnail_width},{$settings.Thumbnails.product_lists_thumbnail_height},product" />
		{include file="common/image.tpl" image_width=$settings.Thumbnails.product_lists_thumbnail_width obj_id=$obj_id_prefix images=$product.main_pair image_height=$settings.Thumbnails.product_lists_thumbnail_height}

		
	<!--list_image_update_{$obj_prefix}{$obj_id}--></span>
	<div class="ty-product-list__rating">
		{assign var="rating" value="rating_$obj_id"}
		{$smarty.capture.$rating nofilter}
	</div>
                    