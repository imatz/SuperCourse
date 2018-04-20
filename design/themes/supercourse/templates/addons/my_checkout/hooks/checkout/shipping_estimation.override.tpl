
                    {foreach from=$product_groups key=group_key item=group name="s"}
                       
                        {if !"ULTIMATE"|fn_allowed_for || $product_groups|count > 1}
                            <ul>
                            {foreach from=$group.products item="product"}
                                <li>
                                    {if $product.product}
                                        {$product.product nofilter}
                                    {else}
                                        {$product.product_id|fn_get_product_name}
                                    {/if}
                                </li>
                            {/foreach}
                            </ul>
                        {/if}

                        {if $group.shippings && !$group.all_edp_free_shipping && !$group.all_free_shipping && !$group.free_shipping && !$group.shipping_no_required}
                            {foreach from=$group.shippings item="shipping" name="estimate_group_shipping"}
                                {if !$show_only_first_shipping || $smarty.foreach.estimate_group_shipping.first}
                                
                                    {if $cart.chosen_shipping.$group_key == $shipping.shipping_id}
                                        {assign var="checked" value="checked=\"checked\""}
                                    {else}
                                        {assign var="checked" value=""}
                                    {/if}

                                    {if $shipping.delivery_time}
                                        {assign var="delivery_time" value="(`$shipping.delivery_time`)"}
                                    {else}
                                        {assign var="delivery_time" value=""}
                                    {/if}

                                    {hook name="checkout:shipping_estimation_method"}
                                    {if $shipping.rate}
                                        {capture assign="rate"}{include file="common/price.tpl" value=$shipping.rate}{/capture}
                                        {if $shipping.inc_tax}
                                            {assign var="rate" value="`$rate` ("}
                                            {if $shipping.taxed_price && $shipping.taxed_price != $shipping.rate}
                                                {capture assign="tax"}{include file="common/price.tpl" value=$shipping.taxed_price class="ty-nowrap"}{/capture}
                                                {assign var="rate" value="`$rate` (`$tax` "}
                                            {/if}
                                            {assign var="inc_tax_lang" value=__('inc_tax')}
                                            {assign var="rate" value="`$rate``$inc_tax_lang`)"}
                                        {/if}
                                    {else}
                                        {assign var="rate" value=__("free_shipping")}
                                    {/if}

                                    <p>
                                        <input type="radio" class="ty-valign" id="sh_{$group_key}_{$shipping.shipping_id}" name="shipping_ids[{$group_key}]" value="{$shipping.shipping_id}" onclick="fn_calculate_total_shipping();" {$checked} /><label for="sh_{$group_key}_{$shipping.shipping_id}" class="ty-valign">{$shipping.shipping} {$delivery_time} - {$rate nofilter}</label>
                                    </p>
                                    {/hook}
                                {/if}
                            {/foreach}

                        {else}
                            {if $group.all_edp_free_shipping || $group.shipping_no_required}
                                <p>{__("no_shipping_required")}</p>
                            {elseif $group.all_free_shipping || $group.free_shipping}
                                <p>{__("free_shipping")}</p>
                            {else}
                                <p>{__("text_no_shipping_methods")}</p>
                            {/if}
                        {/if}

                    {/foreach}

                    <p><strong>{__("total")}:</strong>&nbsp;{include file="common/price.tpl" value=$cart.display_shipping_cost class="ty-price"}</p>
