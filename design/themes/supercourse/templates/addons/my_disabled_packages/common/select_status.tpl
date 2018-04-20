{if $display == "select"}
<select class="input-small {if $meta}{$meta}{/if}" name="{$input_name}" {if $input_id}id="{$input_id}"{/if}>
    <option value="A" {if $obj.status == "A"}selected="selected"{/if}>{__("active")}</option>
    {if $hidden}
    <option value="H" {if $obj.status == "H"}selected="selected"{/if}>{__("hidden")}</option>
    {/if}
    <option value="D" {if $obj.status == "D"}selected="selected"{/if}>{__("disabled")}</option>
</select>

{elseif $display == "text"}
{assign var="selected_st" value=$obj.status|default:"A"}
{capture name="status_title"}
    {if $selected_st == "A"}
        {__("active")}
    {elseif $selected_st == "H"}
        {__("hidden")}
    {elseif $selected_st == "D"}
        {__("disabled")}
    {/if}
{/capture}
<div class="control-group">
    <div class="controls">
    <span>
    {$smarty.capture.status_title nofilter}
    </span>
    </div>
</div>
{/if}

{*<div class="ty-dropdown-box" id="product_status_{$product.product_id}">
	<div id="sw_dropdown_{$product.$product_id}" class="ty-dropdown-box__title">
		<a class="">
			{$product.status}
			<i class="ty-icon-down-micro></i>
		</a>
	</div>
	<div id="dropdown_{$product.$product_id}" class="cm-popup-box ty-dropdown-box__content hidden">
	<div id="account_info_136">
		<ul class="ty-account-info">
		<li class="ty-account-info__item  ty-account-info__name ty-dropdown-box__item">ΑΣΛΑΟΥΡΙΔΟΥ-ΚΡΥΠΩΤΟΥ </li>
		<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="http://localhost/cs433/index.php?dispatch=profiles.update" rel="nofollow">Λεπτομέρειες προφίλ</a></li>

		<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="http://localhost/cs433/index.php?dispatch=orders.vouchers" rel="nofollow">Παραγγελίες</a></li>

		<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="http://localhost/cs433/index.php?dispatch=products.add" rel="nofollow">Νέα σύνθεση</a></li>
		<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="http://localhost/cs433/index.php?dispatch=products.manage" rel="nofollow">_my_packages</a></li>


		</ul>



	<!--account_info_136--></div>
	</div>*}