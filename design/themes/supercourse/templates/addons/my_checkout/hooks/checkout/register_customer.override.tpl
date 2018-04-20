 <div class="ty-checkout__register checkout-register">
        {capture name="register"}
            {*if $settings.General.approve_user_profiles != "Y"*}
                <div id="register_checkout" class="ty-checkout-buttons{if $settings.Checkout.sign_in_default_action != "register"} cm-noscript{/if}">
                    <a href="{"profiles.register"|fn_url}" rel="nofollow" class="ty-btn ty-btn__primary">{__("register")}</a>			
                </div>
            {*/if*}
        {/capture}
        
        {capture name="anonymous"}
            {if $settings.Checkout.disable_anonymous_checkout != "Y"}
                <div id="anonymous_checkout" class="{if $settings.Checkout.sign_in_default_action == "register"} cm-noscript{/if}">
                    <form name="step_one_anonymous_checkout_form" class="{$ajax_form}" action="{""|fn_url}" method="post">
                        <input type="hidden" name="result_ids" value="checkout*,account*" />
                        <input type="hidden" name="guest_checkout" value="1" />

                        {if !$contact_fields_filled}
                            <div class="ty-control-group ty-profile-field__item ty-e-mail">
                                <label for="guest_email" class="ty-control-group__title cm-profile-field cm-required cm-email">{__("email")}</label>
                                <input type="text" id="guest_email" name="user_data[email]" size="32" value="" class="ty-input-text" />
                            </div>
                        {/if}

                        <div class="ty-checkout-buttons">
                            {include file="buttons/button.tpl" but_meta="ty-btn__primary" but_name="dispatch[checkout.customer_info]" but_text=__("checkout_as_guest")}
                        </div>
                    </form>
                </div>
            {/if}
        {/capture}

        <div class="ty-checkout__register-content">
            {if $settings.General.approve_user_profiles != "Y" || $settings.Checkout.disable_anonymous_checkout != "Y"}
                {include file="common/subheader.tpl" title=__("new_customer")}
            {/if}

            <ul class="ty-checkout__register-methods">
                {capture name="checkout_new_customer_register"}
                <li class="ty-checkout__register-methods-item">
                    <input class="ty-checkout__register-methods-radio" type="radio" id="checkout_type_register" name="checkout_type" value=""{if $settings.Checkout.sign_in_default_action == "register"} checked="checked"{/if} onclick="fn_show_checkout_buttons('register')" />
                    <label for="checkout_type_register">
                        <span class="ty-checkout__register-methods-title">{__("register")}</span>
                        <span class="ty-checkout__register-methods-hint">{__("create_new_account")}</span>
                    </label>
                </li>
                {/capture}

                {capture name="checkout_new_customer_guest"}
                {if $settings.Checkout.disable_anonymous_checkout != "Y"}
                    <li class="ty-checkout__register-methods-item">
                        <input class="ty-checkout__register-methods-radio" type="radio" id="checkout_type_guest" name="checkout_type" value=""{if $settings.Checkout.sign_in_default_action != "register"} checked="checked"{/if} onclick="fn_show_checkout_buttons('guest')" />
                        <label for="checkout_type_guest">
                            <span class="ty-checkout__register-methods-title">{__("checkout_as_guest")}</span>
                            <span class="ty-checkout__register-methods-hint">{__("create_guest_account")}</span>
                        </label>
                    </li>
                {/if}
                {/capture}

                {if $settings.Checkout.sign_in_default_action == "register"}
                    {$smarty.capture.checkout_new_customer_register nofilter}
                    {$smarty.capture.checkout_new_customer_guest nofilter}
                {else}
                    {$smarty.capture.checkout_new_customer_guest nofilter}
                    {$smarty.capture.checkout_new_customer_register nofilter}
                {/if}
            </ul>
        </div>

        {$smarty.capture.register nofilter}
        {$smarty.capture.anonymous nofilter}
    </div>