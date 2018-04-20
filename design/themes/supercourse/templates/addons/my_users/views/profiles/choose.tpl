 <form id="form" action="{""|fn_url}" method="post" name="profile_choice_form" >
  <table class="ty-table ty-profile-choice">
    
  {foreach from=$profiles item="profile"}
    <tr>
      <td width = "5%"><input type="radio" name="profile_id" value="{$profile.profile_id}"></td>
      <td><ul>
        {foreach from=$profile_fields key="f" item="pf"}
          <li>{$pf}: {$profile.$f}
        {/foreach}
        </ul>
      </td>
    </tr>  
  {/foreach}
  </table> 
  
  {include file="buttons/button.tpl" but_text=__("choose") but_name="dispatch[profiles.choose]" but_role="submit" but_meta="ty-btn__primary"}
  </form>
  {capture name="mainbox_title"}{__("choose_profile")}{/capture}
