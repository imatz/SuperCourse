{include file="views/profiles/components/profiles_scripts.tpl"}

    <div class="ty-account">
    
        <form name="profiles_register_form" action="{""|fn_url}" method="post"> 
            {include file="views/profiles/components/profiles_account.tpl"}
            {include file="views/profiles/components/profile_fields.tpl" section="C" nothing_extra="Y"}
            {include file="views/profiles/components/profile_fields.tpl" section="S" nothing_extra="Y"}
            
           
            {hook name="checkout:checkout_steps"}{/hook}

            {include file="common/image_verification.tpl" option="use_for_register" align="left" assign="image_verification"}
            {if $image_verification}
            <div class="ty-control-group">
                {$image_verification nofilter}
            </div>
            {/if}

            <div class="ty-profile-field__buttons buttons-container">
                {include file="buttons/register_profile.tpl" but_name="dispatch[profiles.register]"}
            </div>
        </form>
    </div>
    {capture name="mainbox_title"}{__("register_new_account")}{/capture}