<h3>&nbsp;&nbsp;Επιλογή Χρήστη</h3>
<form id="form" action="{""|fn_url}" method="post" name="user_choice_form">
  <table class="ty-table ty-profile-choice">
  {foreach from=$udata_array key="d" item="usid" name="users"}
    <tr>
      <td width = "5%"><input type="radio" name="user_id" value="{$usid.user_id}" {if $smarty.foreach.users.first}checked="checked"{/if}></td>
      <td><ul>
        	<li>{$usid.firstname} {$usid.lastname}
            <li>{$usid.fields['41']} 
            <li>{$usid.fields['42']}
        </ul>
      </td>
    </tr>
  {/foreach}
  </table> 
  
  {include file="buttons/button.tpl" but_text=__("choose") but_name="dispatch[my_changes.my_pass]" but_role="select" but_meta="ty-btn__primary"}
 </form>