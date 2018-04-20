{capture name="mainbox"}

<table width="100%" class="table table-middle">
<thead>
<tr>
    <th>Module</th>
	<th>Status</th>
	<th>Begin</th>
	<th>Last Activity</th>
	<th>End</th>
</tr>
</thead>
{foreach from=$activity item=row}
<tr{if "A"==$row.status} class="warning"{/if}>
    <td class="row-status">{$row.module}</a></td>
    <td class="row-status">{$row.status}</a></td>
    <td class="row-status">{$row.begin_timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
    <td class="row-status">{$row.last_activity_timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
    <td class="row-status">{$row.end_timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
</tr>	
{/foreach}
</table>
{/capture}

{capture name="sidebar"}
 <div class="sidebar-row" id="views">
	<h6>Control Actions<h6>
	<ul class="nav nav-list saved-search">
		<li><a  href="javascript:location.reload(true)">Refresh Data</a></li>
        <li><a class="text-warning" href="{"erp_sync.reset"|fn_url}">Reset Activity</a></li>
        <li><a target="blank" href="{$config.http_location}/erp_sync.php?key={$settings.Security.cron_password}"><em>Fire ERP Sync ~</em></a></li>
	</ul>	
 </div>
	<hr>
{/capture}

{include file="common/mainbox.tpl" title=$_title content=$smarty.capture.mainbox sidebar=$smarty.capture.sidebar  content_id="erp_sync_activity_status"}