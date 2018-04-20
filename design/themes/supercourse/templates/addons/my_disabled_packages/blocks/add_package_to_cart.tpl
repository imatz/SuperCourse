{*Don't show in profiles*}
{if 'profiles'!=$runtime.controller}
<form action="{""|fn_url}" method="post" name="add_package_to_cart_form" enctype="multipart/form-data" class="cm-ajax cm-ajax-full-render cm-ajax-status-middle">
<input type="hidden" name="result_ids" value="cart_status*,wish_list*,checkout*,account_info*">
<input type="hidden" name="redirect_url" value="index.php">
<input type="text" class="ty-input-text-medium cm-hint" placeholder="{__("enter_package_code")}" name="package_code">
{include file="buttons/add_to_cart.tpl" but_id="button_cart_package" but_name="dispatch[checkout.add.package]" but_role="action"}
</form>
{/if}