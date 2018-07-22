{capture name="section"}
    {include file="addons/my_order_vouchers/views/my_order_vouchers/components/orders_search_form.tpl"}
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
            <th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=customer&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("customer")}</a>{if $search.sort_by == "customer"}{$sort_sign nofilter}{/if}</th>
            <th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=date&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("date")}</a>{if $search.sort_by == "date"}{$sort_sign nofilter}{/if}</th>
			<th><a class="{$ajax_class}" href="{"`$c_url`&sort_by=total&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id="pagination_contents">{__("total")}</a>{if $search.sort_by == "total"}{$sort_sign nofilter}{/if}</th>
			<th>{__("vouchers")}</th>
        </tr>
    </thead>
    {foreach from=$orders item="o"}
        <tr>
            <td class="ty-orders-search__item"><strong>#{$o.order_id}</strong></td>
            <td class="ty-orders-search__item">
                <ul class="ty-orders-search__user-info">
                    <li class="ty-orders-search__user-name">{$o.firstname} {$o.lastname}</li>
                    <li  class="ty-orders-search__user-mail"><a href="mailto:{$o.email|escape:url}">{$o.email}</a></li>
                </ul>
            </td>
            <td class="ty-orders-search__item"><a href="{"orders.details?order_id=`$o.order_id`"|fn_url}">{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</a></td>
            <td class="ty-orders-search__item">{include file="common/price.tpl" value=$o.total}</td>
			<td class="ty-orders-search__item">
			<ul>
				{foreach from=$o.vouchers item="v" key="i"}
				<li><a href="{"my_order_vouchers.get_voucher?order_id=`$o.order_id`&voucher=`$i+1`"|fn_url}">{$v}</a></li>
				{foreachelse}
				<li>&nbsp;</li>
				{/foreach}
			</ul>	
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