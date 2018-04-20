{capture name="main_icon"}
	<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
		{include file="common/image.tpl" obj_id=$obj_id_prefix images=$product.main_pair image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height}
	</a>
{/capture}

{if $product.image_pairs && $show_gallery}
<div class="ty-center-block flip-container{if $addons.fliping_product_icons.show_effect == 'vertical_flip'} vertical{/if}">
	<div class="ty-thumbs-wrapper cm-image-gallery flipper">

			{if $product.main_pair}
				<div class="{if $addons.fliping_product_icons.show_effect == 'none'}cm-gallery-item{else}front{/if}">
					{$smarty.capture.main_icon nofilter}
				</div>
			{/if}

			{foreach from=$product.image_pairs item="image_pair"}
				{if $image_pair}
					<div class="{if $addons.fliping_product_icons.show_effect == 'none'}cm-gallery-item-second{else}back{/if}">
						<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
							{include file="common/image.tpl" no_ids=true images=$image_pair image_width=$settings.Thumbnails.product_lists_thumbnail_width image_height=$settings.Thumbnails.product_lists_thumbnail_height}
						</a>
					</div>
					{break}
				{/if}
			{/foreach}

	</div>
</div>
{else}
	{$smarty.capture.main_icon nofilter}
{/if}