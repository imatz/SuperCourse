{** block-description:tmpl_copyright **}
<p class="bottom-copyright">&copy; {if $smarty.const.TIME|date_format:"%Y" != $settings.Company.company_start_year}{$settings.Company.company_start_year}-{/if}{$smarty.const.TIME|date_format:"%Y"} {$settings.Company.company_name}. &nbsp;{__("powered_by")} <a class="bottom-copyright" href="//www.cart-power.com/" target="_blank">{__("copyright_cart_power", ["[product]" => $smarty.const.PRODUCT_NAME])}</a>
</p>