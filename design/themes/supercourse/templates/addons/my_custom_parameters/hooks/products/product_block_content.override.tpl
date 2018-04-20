 {if $js_product_var}
      <input type="hidden" id="product_{$obj_prefix}{$product.product_id}" value="{$product.product}" />
  {/if}
  {* res_delete_1 *}
  {if $item_number == "Y"}<strong>{$smarty.foreach.products.iteration}.&nbsp;</strong>{/if}
  {assign var="sku" value="sku_$obj_id"}{$smarty.capture.$sku nofilter}
  {* /res_delete_1 *}

  <div class="ty-product-list__info">
      <div class="ty-product-list__item-name">
          {assign var="name" value="name_$obj_id"}
          {$smarty.capture.$name nofilter}
      </div>

      <div class="ty-product-list__price">
          {assign var="old_price" value="old_price_`$obj_id`"}
          {if $smarty.capture.$old_price|trim}
              {$smarty.capture.$old_price nofilter}
          {/if}

          {assign var="price" value="price_`$obj_id`"}
          {$smarty.capture.$price nofilter}

          {assign var="clean_price" value="clean_price_`$obj_id`"}
          {$smarty.capture.$clean_price nofilter}

          {assign var="list_discount" value="list_discount_`$obj_id`"}
          {$smarty.capture.$list_discount nofilter}
      </div>
      
      
      <div class="ty-product-list__feature">
          {assign var="product_features" value="product_features_`$obj_id`"}
          {$smarty.capture.$product_features nofilter}
      </div>
          
      {if !$smarty.capture.capt_options_vs_qty}
          <div class="ty-product-list__option">
              {assign var="product_options" value="product_options_`$obj_id`"}
              {$smarty.capture.$product_options nofilter}
          </div>

          {assign var="product_amount" value="product_amount_`$obj_id`"}
          {$smarty.capture.$product_amount nofilter}
          
          <div class="ty-product-list__qty">
              {assign var="qty" value="qty_`$obj_id`"}
              {$smarty.capture.$qty nofilter}
          </div>
      {/if}

      {assign var="advanced_options" value="advanced_options_`$obj_id`"}
      {$smarty.capture.$advanced_options nofilter}

      {assign var="min_qty" value="min_qty_`$obj_id`"}
      {$smarty.capture.$min_qty nofilter}

      {assign var="product_edp" value="product_edp_`$obj_id`"}
      {$smarty.capture.$product_edp nofilter}
  </div>
  
  <div class="ty-product-list__control">
      {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
      {$smarty.capture.$add_to_cart nofilter}
  </div>
 
</div>

<div class="ty-product-list__description">
    {assign var="prod_descr" value="prod_descr_`$obj_id`"}
    {$smarty.capture.$prod_descr nofilter}
 