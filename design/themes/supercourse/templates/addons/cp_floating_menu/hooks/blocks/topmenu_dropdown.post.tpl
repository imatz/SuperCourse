<script> 
    function fn_check_top_panel_fixed() {
        if ($(window).width() > 767) {     
            $('.cp-top-panel').parent('.tygh-top-panel').css('left', 0);
            $('.cp-top-panel').parent('.tygh-top-panel').css('right', 0);    
            $('.cp-top-panel').parent('.tygh-top-panel').css('position', 'fixed');    
            $('.cp-top-panel').parent('.tygh-top-panel').css('z-index', '999');
        } else {
            $('.cp-top-panel').parent('.tygh-top-panel').css('position', 'relative');                
        }
    }

    fn_check_top_panel_fixed();
    
    $(window).resize(function () {    
        fn_check_top_panel_fixed();
    });
        
    $(window).scroll(function () {
        if ($(window).width() > 767) {     
            if ($(window).scrollTop() < 90) {
                $('#cp_f_menu').animate({ "top": "-50px" }, "slow" );
                $('#cp_f_menu').css('display', 'none'); 
                $('#cp_f_menu').css('background', '#ffffff');
                
                $('li.cm-menu-item-responsive').children('div').children('a').children('img').removeAttr('width');
                $('li.cm-menu-item-responsive').children('div').children('a').children('img').removeAttr('height');             
            }
        
            if ($(window).scrollTop() > 90 && $('#cp_f_menu').css('display') == 'none') {
                $('#cp_f_menu').css('display', 'block');
                               
                if ($('div.container-fluid').hasClass('cp-top-panel') === false) {
                    var cp_top = '0px';                
                } else {
                    var cp_top = '34px';
                }
                
                $('#cp_f_menu').animate({ "top": cp_top }, "slow");
                $('#cp_f_menu').css('background', '#ffffff');             
            }
        }
    });
</script>

{if $items}
<div id="cp_f_menu" style="position: fixed; top:-50px; left:0; right: 0; z-index: 998; text-align: center; background: #ffffff; display: none; box-shadow: 0 1px 4px -1px rgba(0,0,0,0.7); height: 47px;">
   <ul class="ty-menu__items cm-responsive-menu" style="display: inline-block;">
         <li class="ty-menu__item  cm-menu-item-responsive cp-first-li-logo">
         {include file="blocks/static_templates/logo.tpl"}
         </li>
        {hook name="blocks:topmenu_dropdown_top_menu"}
            <li class="ty-menu__item ty-menu__menu-btn visible-phone">
                <a class="ty-menu__item-link">
                    <i class="ty-icon-short-list"></i>
                    <span>{__("menu")}</span>
                </a>
            </li>

        {foreach from=$items item="item1" name="item1"}
            {assign var="item1_url" value=$item1|fn_form_dropdown_object_link:$block.type}
            {assign var="unique_elm_id" value=$item1_url|md5}
            {assign var="unique_elm_id" value="topmenu_`$block.block_id`_`$unique_elm_id`"}

            {if $subitems_count}

            {/if}
            <li class="ty-menu__item {if !$item1.$childs} ty-menu__item-nodrop{else} cm-menu-item-responsive{/if} {if $item1.active || $item1|fn_check_is_active_menu_item:$block.type} ty-menu__item-active{/if}">
                    {if $item1.$childs}
                        <a class="ty-menu__item-toggle visible-phone cm-responsive-menu-toggle">
                            <i class="ty-menu__icon-open ty-icon-down-open"></i>
                            <i class="ty-menu__icon-hide ty-icon-up-open"></i>
                        </a>
                    {/if}
                    <a {if $item1_url} href="{$item1_url}"{/if} class="ty-menu__item-link">
                        {$item1.$name}
                    </a>
                {if $item1.$childs}

                    {if !$item1.$childs|fn_check_second_level_child_array:$childs}
                    {* Only two levels. Vertical output *}
                        <div class="ty-menu__submenu">
                            <ul class="ty-menu__submenu-items ty-menu__submenu-items-simple cm-responsive-menu-submenu">
                                {hook name="blocks:topmenu_dropdown_2levels_elements"}

                                {foreach from=$item1.$childs item="item2" name="item2"}
                                    {assign var="item_url2" value=$item2|fn_form_dropdown_object_link:$block.type}
                                    <li class="ty-menu__submenu-item{if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-active{/if}">
                                        <a class="ty-menu__submenu-link" {if $item_url2} href="{$item_url2}"{/if}>{$item2.$name}</a>
                                    </li>
                                {/foreach}
                                {if $item1.show_more && $item1_url}
                                    <li class="ty-menu__submenu-item ty-menu__submenu-alt-link">
                                        <a href="{$item1_url}"
                                           class="ty-menu__submenu-alt-link">{__("text_topmenu_view_more")}</a>
                                    </li>
                                {/if}

                                {/hook}
                            </ul>
                        </div>
                    {else}
                        <div class="ty-menu__submenu" id="{$unique_elm_id}">
                            {hook name="blocks:topmenu_dropdown_3levels_cols"}
                                <ul class="ty-menu__submenu-items cm-responsive-menu-submenu">
                                    {foreach from=$item1.$childs item="item2" name="item2"}
                                        <li class="ty-top-mine__submenu-col">
                                            {assign var="item2_url" value=$item2|fn_form_dropdown_object_link:$block.type}
                                            <div class="ty-menu__submenu-item-header {if $item2.active || $item2|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-header-active{/if}">
                                                <a{if $item2_url} href="{$item2_url}"{/if} class="ty-menu__submenu-link">{$item2.$name}</a>
                                            </div>
                                            {if $item2.$childs}
                                                <a class="ty-menu__item-toggle visible-phone cm-responsive-menu-toggle">
                                                    <i class="ty-menu__icon-open ty-icon-down-open"></i>
                                                    <i class="ty-menu__icon-hide ty-icon-up-open"></i>
                                                </a>
                                            {/if}
                                            <div class="ty-menu__submenu">
                                                <ul class="ty-menu__submenu-list cm-responsive-menu-submenu">
                                                    {if $item2.$childs}
                                                        {hook name="blocks:topmenu_dropdown_3levels_col_elements"}
                                                        {foreach from=$item2.$childs item="item3" name="item3"}
                                                            {assign var="item3_url" value=$item3|fn_form_dropdown_object_link:$block.type}
                                                            <li class="ty-menu__submenu-item{if $item3.active || $item3|fn_check_is_active_menu_item:$block.type} ty-menu__submenu-item-active{/if}">
                                                                <a{if $item3_url} href="{$item3_url}"{/if}
                                                                        class="ty-menu__submenu-link">{$item3.$name}</a>
                                                            </li>
                                                        {/foreach}
                                                        {if $item2.show_more && $item2_url}
                                                            <li class="ty-menu__submenu-item ty-menu__submenu-alt-link">
                                                                <a href="{$item2_url}"
                                                                   class="ty-menu__submenu-link">{__("text_topmenu_view_more")}</a>
                                                            </li>
                                                        {/if}
                                                        {/hook}
                                                    {/if}
                                                </ul>
                                            </div>
                                        </li>
                                    {/foreach}
                                    {if $item1.show_more && $item1_url}
                                        <li class="ty-menu__submenu-dropdown-bottom">
                                            <a href="{$item1_url}">{__("text_topmenu_more", ["[item]" => $item1.$name])}</a>
                                        </li>
                                    {/if}
                                </ul>
                            {/hook}
                        </div>
                    {/if}

                {/if}
            </li>
        {/foreach}

        {/hook}
    </ul>
</div>    
{/if}