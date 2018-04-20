{capture name="phone_search"}
<div class="sidebar-field">
    <label for="phone">{__("phone")}</label>
    <input type="text" name="phone" id="phone" value="{$search.phone}" size="30" />
</div>
{/capture}

{include file="views/orders/components/../components/orders_search_form.tpl" dispatch="orders.manage" extra=$smarty.capture.phone_search}