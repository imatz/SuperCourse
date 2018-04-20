{if $content|trim}
	{include file="common/popupbox.tpl" id="ecl_popup_`$block.block_id`" link_text=$title content=$content link_meta="hidden" text=$title|unescape}

	{$delay = 0}
	{if $block.user_class}
		{$user_classes = " "|explode:$block.user_class}
		{foreach from=$user_classes item=uc}
			{if $uc|strpos:'cm-delay-' !== false}
				{$delay = $uc|replace:'cm-delay-':''}
			{/if}
		{/foreach}
	{/if}

	<script type="text/javascript">
	//<![CDATA[
	(function(_, $) {
        $(document).ready(function(){
			if ($.cookie.get('ecl_popup_{$block.block_id}') == null) {
				setTimeout(function(){
					$('#opener_ecl_popup_{$block.block_id}').trigger('click');
				}, {$delay});
			}
			$('[aria-describedby="content_ecl_popup_{$block.block_id}"] button').on('click', function() {
				$.cookie.set('ecl_popup_{$block.block_id}', 1);
			});
		});
	}(Tygh, Tygh.$));
	//]]>
	</script>
{/if}