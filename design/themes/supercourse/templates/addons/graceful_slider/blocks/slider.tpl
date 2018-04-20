{** block-description:slider **}

{if $items}
	<ul id="graceful_slider_{$block.snapping_id}" class="grslider">
	{$v_video = false}
	{$v_youtube = false}
	{$v_vimeo = false}
	{$t_hover_style = ''}
	{foreach from=$items item="slide" key="key"}
		<li{if $slide.main_pair.image_id && $slide.type == "V"} style="background-image: url({$slide.main_pair.icon.image_path})"{/if} class="slide_id_{$slide.slide_id}">
		{if $slide.type == "G" && $slide.main_pair.image_id}
			<div class="grslider-center-wrapper only-image{if $block.properties.image_width_full == "Y"} image_width_full{/if}">
				{if $slide.url != ""}<a href="{$slide.url|fn_url}" {if $slide.target == "B"}target="_blank"{/if}>{/if}
					{include file="common/image.tpl" images=$slide.main_pair}
				{if $slide.url != ""}</a>{/if}
			</div>
		{else if  $slide.type == "V" }
			{$v_video = true}
			{if $v_youtube == false && $slide.video_code|strpos:"youtube" != false}{$v_youtube = true}{/if}
			{if $v_vimeo == false && $slide.video_code|strpos:"vimeo" != false}{$v_vimeo = true}{/if}
			<div class="grslider-center-wrapper">
				{$slide.video_code nofilter}
			<div class="grslider-center-wrapper">
		{else if  $slide.type == "T" }
			<div class="grslider-center-wrapper only-image {if $block.properties.image_width_full == "Y"}image_width_full{/if} grslider-hover-box {$slide.settings.hover_effects}" style="background-color: {$slide.settings.slide_bg_color};">

				{$t_hover_style = "
					`$t_hover_style`
					.slide_id_`$slide.slide_id` img {
						-moz-opacity: `$slide.settings.slide_bg_image_opacity/100`;
						opacity: `$slide.settings.slide_bg_image_opacity/100`;
						filter: alpha(opacity=`$slide.settings.slide_bg_image_opacity`);
					}
					.slide_id_`$slide.slide_id` h3,
					.slide_id_`$slide.slide_id` article {
						color: `$slide.settings.slide_text_color`;
					}
					.gr-wrapper:hover .slide_id_`$slide.slide_id` h3,
					.gr-wrapper:hover .slide_id_`$slide.slide_id` article {
						color: `$slide.settings.slide_text_color_hover`;
					}
					.gr-wrapper:hover .slide_id_`$slide.slide_id` div:before {
						background-color: {$slide.settings.slide_bg_color_hover};
						-moz-opacity: `$slide.settings.slide_bg_color_opacity_hover/100`;
						opacity: `$slide.settings.slide_bg_color_opacity_hover/100`;
						filter: alpha(opacity=`$slide.settings.slide_bg_color_opacity_hover`);
					}
				"}
			
				{if $slide.url != ""}<a href="{$slide.url|fn_url}" {if $slide.target == "B"}target="_blank"{/if}>{/if}
					{include file="common/image.tpl" images=$slide.main_pair}
				{if $slide.url != ""}</a>{/if}
				
				{if $slide.url != ""}<a href="{$slide.url|fn_url}" {if $slide.target == "B"}target="_blank"{/if}>{/if}
					<div class="infolayer">
						<h3>{$slide.title nofilter}</h3>
						<article>{$slide.description nofilter}</article>
					</div>
				{if $slide.url != ""}</a>{/if}

			</div>
		{/if}
		</li>
	{/foreach}
	</ul>

	<style>
		{if $block.properties.full_width != 'N'}
			.gr-wrapper {
				position: absolute;
				left: 0;
				right: 0;
				margin-bottom: 30px;
			}
			
			{if $block.properties.limit_central == 'Y'}
			.graceful_slider-{$block.snapping_id} .grslider-center-wrapper {
				max-width: {$block.properties.limit_central_max_size}px;
			}
			{/if}
		{/if}
		.graceful_slider-{$block.snapping_id}.gr-wrapper .gr-viewport {
			background: {$block.properties.slider_bg|default:"#F6F5F3"};
		}
		{$t_hover_style}
	</style>


	{if $v_vimeo}<script type="text/javascript" src="http://a.vimeocdn.com/js/froogaloop2.min.js"></script>{/if}
	<script type="text/javascript">
	//<![CDATA[
		$(document).ready(function(){

			{if $v_youtube}
			function youtubeVideoPause() {
				{foreach from=$items item="slide" key="key"}
					{if $slide.type == "V" && $slide.video_code|strpos:"youtube" !== false}
						$('#slide_{$slide.slide_id}'){literal}[0].contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*');{/literal}
					{/if}
				{/foreach}
				
			};
			{/if}
			{if $v_vimeo}
			function vimeoVideoPause() {
				$('iframe[src*="vimeo.com"]').each(function () {
					$f(this).api('pause');
				});
			}
			{/if}

			$('#graceful_slider_{$block.snapping_id}').bxSlider({
				wrapperClass: 'gr-wrapper {$block.properties.skin} graceful_slider-{$block.snapping_id}',
				{if $block.properties.mode == "H"}
					mode: 'horizontal',
				{/if}
				{if $block.properties.mode == "V"}
					mode: 'vertical',
				{/if}
				{if $block.properties.mode == "F"}
					mode: 'fade',
				{/if}

				pause: '{$block.properties.delay * 1000|default:4000}',
					
				{if $block.properties.auto == "Y"}
					auto: true,
				{/if}
				{if $block.properties.controls != "Y"}
					controls: false,
				{/if}
				{if $block.properties.auto == "Y" && $block.properties.auto_controls == "Y"}
					autoControls: true,
				{/if}
				{if $block.properties.pager == "N"}
					pager: false,
				{/if}
				{if $block.properties.pager == "S"}
					pagerType: 'short',
				{/if}
				{if $block.properties.infinite_loop == "N"}
					infiniteLoop: false,
					hideControlOnEnd: true,
				{/if}

				useCSS: false,
				autoHover: true,
				
				{if $v_video == true}
					video: true,
				{/if}

				{if $v_youtube || $v_vimeo}
				onSlideAfter: function(slide){
					{if $v_youtube}youtubeVideoPause();{/if}
					{if $v_vimeo}vimeoVideoPause();{/if}
				}
				{/if}

			});

		});
	//]]>
	</script>
{/if}
