{capture name="section"}
    {include file="addons/my_order_progress/views/my_order_progress/components/orders_search_form.tpl"}
{/capture}
{include file="common/section.tpl" section_title=__("search_options") section_content=$smarty.capture.section class="ty-search-form"}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
{if $search.sort_order == "asc"}
{assign var="sort_sign" value="<i class=\"ty-icon-down-dir\"></i>"}
{else}
{assign var="sort_sign" value="<i class=\"ty-icon-up-dir\"></i>"}
{/if}
{if !$config.tweaks.disable_dhtml}
    {assign var="ajax_class" value="cm-ajax"}

{/if}

{include file="common/pagination.tpl"}

<table class="ty-table ty-orders-search">
    <thead>
        <tr>
            <th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=order_id&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("id")}</a>{if $search.sort_by == "order_id"}{$sort_sign nofilter}{/if}</th>
			<th>{__("status")}</th>
            <th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=customer&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("customer")}</a>{if $search.sort_by == "customer"}{$sort_sign nofilter}{/if}</th>
            <th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=date&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("date")}</a>{if $search.sort_by == "date"}{$sort_sign nofilter}{/if}</th>
			<th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=total&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("total")}</a>{if $search.sort_by == "total"}{$sort_sign nofilter}{/if}</th>
			<th>{__("actions")}</th>
        </tr>
    </thead>
    {foreach from=$orders item="o"}
		{assign var="progress_status" value=$o|fn_get_order_progress_status}
        <tr>
            <td class="ty-orders-search__item"><strong>#{$o.order_id}</strong></td>
            <td class="ty-orders-search__item">{$progress_status|fn_get_order_progress_status_desc}</td>
            <td class="ty-orders-search__item">
                <ul class="ty-orders-search__user-info">
                    <li class="ty-orders-search__user-name">{$o.firstname}</li>
                    <li  class="ty-orders-search__user-mail"><a href="mailto:{$o.filemail|escape:url}">{$o.email}</a></li>
                </ul>
            </td>
            <td class="ty-orders-search__item"><a href="{"orders.details?order_id=`$o.order_id`"|fn_url}">{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</a></td>
            <td class="ty-orders-search__item">{include file="common/price.tpl" value=$o.total}</td>
			<td class="ty-orders-search__item">
			{if 3==$progress_status}
				{assign var="tracking_url" value=$o|fn_get_order_tracking_link}
				{if $tracking_url}
					<a target="blank" href="{$tracking_url}">{__("track_order")}</a>
				{/if}
			{else if 1==$progress_status}
				<ul>
					<li>{include file="buttons/button.tpl" but_text=__("cancel_order") but_href="my_order_progress.cancel?order_id=`$o.order_id`"}</li>
					<li>{include file="buttons/button.tpl" but_text=__("review_order") but_href="my_order_progress.review?order_id=`$o.order_id`"}</li>
				</ul>	
			{/if}
			</td>
        </tr>
    {foreachelse}
        <tr class="ty-table__no-items">
            <td colspan="7"><p class="ty-no-items">{__("text_no_orders")}</p></td>
        </tr>
    {/foreach}
</table>

{include file="common/pagination.tpl"}

{capture name="mainbox_title"}{__("orders")}{/capture}