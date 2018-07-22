    {capture name="tabsbox"}
        <div class="ty-profile-field ty-account form-wrap" id="content_general">
            <form name="profile_form" action="{""|fn_url}" method="post">
               
                    {include file="views/profiles/components/profiles_account.tpl"}
                    {include file="views/profiles/components/profile_fields.tpl" section="C" title=__("contact_information") id_prefix="profile"}

               
                <div class="ty-profile-field__buttons buttons-container">
					{include file="buttons/save.tpl" but_name="dispatch[user_info.update]" but_meta="ty-btn__secondary" but_id="save_profile_but"}
					<input class="ty-profile-field__reset ty-btn ty-btn__tertiary" type="reset" name="reset" value="{__("revert")}" id="shipping_address_reset"/>
                </div>
            </form>
        </div>
        
    {/capture}

    {$smarty.capture.tabsbox nofilter}

    {capture name="mainbox_title"}{__("profile_details")}{/capture}
