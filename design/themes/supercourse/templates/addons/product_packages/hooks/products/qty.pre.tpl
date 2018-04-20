{if $product.product_id|fn_check_package == "Y" && $details_page}
{include file="common/subheader.tpl" title=__("products_in_package")}
  <div style="padding-top: 15px; padding-bottom: 15px;" id="product_package_update_{$product.product_id}" class="cm-reload-{$product.product_id}">

  {include file="addons/product_packages/components/package_info.tpl" package_info=$product.package_info}
  <!--product_package_update_{$product.product_id}--></div>
{/if}