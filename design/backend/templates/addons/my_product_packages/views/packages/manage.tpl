{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="manage_packages_form" id="manage_packages_form">

{include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

{if $products}
<table width="100%" class="table table-middle">
<thead>
<tr>
    <th class="left">
      {include file="common/check_items.tpl" check_statuses=''|fn_get_default_status_filters:true}
    </th>
    <th width="40%"><a class="cm-ajax" href="{"`$c_url`&sort_by=product&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("name")}{if $search.sort_by == "product"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th width="12%"><a class="{$ajax_class}" href="{"`$c_url`&sort_by=code&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("code")}{if $search.sort_by == "code"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a> /
    <a class="{$ajax_class}" href="{"`$c_url`&sort_by=timestamp&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("creation_date")}{if $search.sort_by == "timestamp"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th width="30%"><a class="{$ajax_class}" href="{"`$c_url`&sort_by=user&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("customer")}{if $search.sort_by == "user"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
    <th width="10%" class="right"><a class="cm-ajax" href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
</tr>
</thead>
{foreach from=$products item=product}


<tr class="cm-row-status-{$product.status|lower}">
    <td class="left">
      <input type="checkbox" name="product_ids[]" value="{$product.product_id}" class="checkbox cm-item cm-item-status-{$product.status|lower}" />
    </td>
    <td>
      <a class="row-status" href="{"products.update?product_id=`$product.product_id`"|fn_url}">{$product.product|truncate:50 nofilter}</a>
      <br><span class="row-status">{include file="addons/my_product_packages/components/package_products.tpl" package_products=$product.package_products}</span>
    </td>
    <td>
      <span class="product-code-label row-status">{$product.product_code}<br>
      <em class="ty-date">{$product.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</em></span>
    </td>
    <td>
      <span class="row-status">
      {$product.user_login}<br>
      {$product.lastname}<br>
      {$product.email}
      </span>
    </td>
    <td class="right nowrap">
    {include file="common/select_popup.tpl" popup_additional_class="dropleft" id=$product.product_id status=$product.status hidden=true object_id_name="product_id" table="products"}
    </td>
</tr>
{/foreach}
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}



{capture name="buttons"}
    {capture name="tools_list"}
		{if $products}
        <li>{btn type="delete_selected" dispatch="dispatch[products.m_delete]" form="manage_packages_form"}</li>
    {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
    
{/capture}
<div class="clearfix">
    {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
</div>

</form>

{/capture}

{capture name="sidebar"}
    {include file="common/saved_search.tpl" dispatch="packages.manage" view_type="products"}
    {include file="addons/my_product_packages/components/products_search_form.tpl" dispatch="packages.manage"}
{/capture}

{include file="common/mainbox.tpl" title=__("product_packages") content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra adv_buttons=$smarty.capture.adv_buttons select_languages=true buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar content_id="manage_packages"}