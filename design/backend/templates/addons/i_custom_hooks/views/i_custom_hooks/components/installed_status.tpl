{*

	Author: Ioannis Matziaris [imatz]
	Email: imatzgr@gmail.com
	Date: October 2013
	Copyrights: All rights are reserved
	Details:

*}

{if $installed=="S"}
	{__("successful")}
{else if $installed=="F"}
	{__("failed")}
{else}
	{__("not_installed")}
{/if}