{*Liampas Stathis Allages stin emfanish stoixeiwn logariasmoy*}
{if $auth.user_id}
	{if $user_info.firstname || $user_info.lastname}
		{*se sxolio to lastname dioti einai eite idio me to onoma eite ws teleia.*}
		<li class="ty-account-info__username">{$user_info.firstname} {*$user_info.lastname*}</li> 
	{else}
		<li class="ty-account-info__item ty-dropdown-box__item ty-account-info__name">{$user_info.email}</li>
	{/if}
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"user_info.update"|fn_url}" rel="nofollow" >{__("profile_details")}</a></li>
    {if $smarty.session.num_of_profiles>1}
	{*emfanish tis dieunthisis toy upokatastimatos an ayti uparxei*}
	{if $auth.profile_name}
	<li class="ty-account-info__profile">{*$auth.profile_id*}{$auth.profile_name}<hr class="account_line"></li>
	{/if}
	{*Telos emfanishs toy upokatastimatos*}
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"my_changes.prof_choice"|fn_url}" rel="nofollow" >{__("epilogi_ypok")}</a></li>
    {/if}
	{*se sxolio ta parastatika dioti den leitourgoun*}
	{*<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="http://kaligasvouchers.dyndns.org/vouchers/index.php" target="blank" rel="nofollow">{__("vouchers")}</a></li>*}
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"my_order_progress.search"|fn_url}" rel="nofollow">{__("order_progress")}</a></li>
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"my_order_vouchers.search"|fn_url}" rel="nofollow">{__("order_vouchers")}</a></li>
	{if $auth.account_type=="B"}
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"products_list.download"|fn_url}" rel="nofollow">{__("download_product_list")}</a></li>
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"products_list.view"|fn_url}" rel="nofollow">{__("upload_product_list")}</a></li>
	{else if $auth.account_type=="S"}
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"products.add"|fn_url}" rel="nofollow">{__("new_package")}</a></li>
	<li class="ty-account-info__item ty-dropdown-box__item"><a class="ty-account-info__a underlined" href="{"index.php?search_performed=Y&status=A&creation=S&dispatch%5Bproducts.manage%5D=&security_hash=529add5edf3be9eb5e97814b92bc9164"|fn_url}" rel="nofollow">{__("my_packages")}</a></li>
	{/if}
{elseif $user_data.firstname || $user_data.lastname}
	<li class="ty-account-info__item  ty-dropdown-box__item ty-account-info__name">{$user_data.firstname} {*$user_data.lastname*}</li>
{elseif $user_data.email}
	<li class="ty-account-info__item ty-dropdown-box__item ty-account-info__name">{$user_data.email}</li>
{else}
&nbsp;	
{/if}