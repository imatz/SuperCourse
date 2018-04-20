{if $content|trim}
    {if $anchor}
    <a name="{$anchor}"></a>
    {/if}
    <div class="mainbox-container clearfix{if isset($hide_wrapper)} cm-hidden-wrapper{/if}{if $hide_wrapper} hidden{/if}{if $details_page} details-page{/if}{if $block.user_class} {$block.user_class}{/if}{if $content_alignment == "RIGHT"} ty-float-right{elseif $content_alignment == "LEFT"} ty-float-left{/if}">
        {if $title || $smarty.capture.title|trim}
            <h2 class="ty-mainbox-title">
                {hook name="wrapper:mainbox_general_title"}
                <div style="margin-left: 20px;">
                		<h2>
                			{if $smarty.capture.title|trim}
                    			{$smarty.capture.title nofilter}
                			{else}
                    			{$title nofilter}
                			{/if}
                		</h2>
                	</div>
                {/hook}
            </h2>
        {/if}
        <div class="ty-mainbox-body">{$content nofilter}</div>
    	</div>
{/if}