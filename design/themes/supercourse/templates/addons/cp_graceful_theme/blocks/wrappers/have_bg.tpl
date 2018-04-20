{if $content|trim}
	<div class="have-bg-class {if $block.user_class} {$block.user_class}{/if}{if $content_alignment == "RIGHT"} ty-float-right{elseif $content_alignment == "LEFT"} ty-float-left{/if}">
		{$content nofilter}
	</div>
{/if}