<div class="ty-search-block">
	
	<a href="#" class="search-open-link">
		<span class="ty-icon-search"></span>
	</a>

    <form class="search_form" action="{""|fn_url}" name="search_form" method="get">
        <input type="hidden" name="subcats" value="Y" />
        <input type="hidden" name="status" value="A" />
        <input type="hidden" name="pshort" value="Y" />
        <input type="hidden" name="pfull" value="Y" />
        <input type="hidden" name="pname" value="Y" />
        <input type="hidden" name="pkeywords" value="Y" />
        <input type="hidden" name="search_performed" value="Y" />

        {hook name="search:additional_fields"}{/hook}

        {strip}
            {if $settings.General.search_objects}
                {assign var="search_title" value=__("search")}
            {else}
                {assign var="search_title" value=__("search_products")}
            {/if}
            <input type="text" name="q" value="{$search.q}" id="search_input{if $smarty.capture.search_input_id}_{$smarty.capture.search_input_id}{/if}" title="{$search_title}" placeholder="{$search_title}" class="ty-search-block__input cm-hint" />
            {if $settings.General.search_objects}
                {include file="buttons/magnifier.tpl" but_name="search.results" alt=__("search")}
            {else}
                {include file="buttons/magnifier.tpl" but_name="products.search" alt=__("search")}
            {/if}
        {/strip}

        {capture name="search_input_id"}
            {math equation="x + y" x=$smarty.capture.search_input_id|default:1 y=1 assign="search_input_id"}
            {$search_input_id}
        {/capture}
    </form>
</div>


<script type="text/javascript">
(function(_, $) {
    $(document).ready(function() {
            $("body").click(function(ev) {
				
		if ( $(ev.target).hasClass('search-open-link') || $(ev.target).hasClass('ty-icon-search') || $(ev.target).hasClass('ty-search-block__input') || $(ev.target).parents('#live_reload_box').attr('id') == 'live_reload_box') {
		  $('.search_form').addClass('ls-visible');
		} else {
		  $('.search_form').removeClass('ls-visible');
		}

            });
    });
}(Tygh, Tygh.$));
</script>
