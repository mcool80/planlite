{*
 *  Planlite - Online planning program in php 
 *  Copyright (C) 2008  Markus Svensson, CelIT
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *}
<script language="JavaScript" src="Include/date-picker.js"></script>
{literal}
<script language="javascript" type="text/javascript">
function getWorktimeid(userid, unitid, year, month, week, day_in_month, worktime)
{
	{/literal}
{foreach name=outer item=item from=$worktimes}
		if ( unitid=={$item.unitid} && userid=={$item.userid} && year == {$item.year} && month=={$item.month} && week=={$item.week} && day_in_month=={$item.day_in_month} )
			if ( worktime != {$item.worktime} )
				return {$item.worktimeid};
			else
				return -1;
{/foreach}

	{literal}
	return 0;
}
function changeWorktime(worktimeid, userid, unitid, worktime )
{
	location.href = 'sida.php?page=worktime&action=editworktime&userid='+userid+'&unitid='+unitid+'&worktimeid='+worktimeid+'&worktime='+worktime+'&year={/literal}{$year}&month={$month}{literal}';
	//alert('sida.php?page=worktime&action=editworktime&worktimeid='+worktimeid+'&userid='+userid+'&unitid='+unitid+'&worktime='+worktime);
}
function addWorktime(worktimeid, userid, unitid, year, month, week, day_in_month, worktime)
{
//	worktimeid = getWorktimeid(year, month, week, day_in_month, worktime);
	if ( worktime == -1 )
		return;
	if ( worktimeid == 0 )
	{
		location.href = 'sida.php?page=worktime&action=editworktime&userid='+userid+'&unitid='+unitid+'&year='+year+'&month='+month+'&week='+week+'&day_in_month='+day_in_month+'&worktime='+worktime;
	}
	else
		changeWorktime(worktimeid, userid, unitid, worktime );
}
function showWorktime(userunitid)
{
	if ( userunitid.substring(0,4) == 'unit')
	{
		unitid = userunitid.substring(4);
		userid = '';
	}
	else
	{
		userid = userunitid.substring(4);
		unitid = '';
	}
{/literal}
	location.href = 'sida.php?page=worktime&action=editworktimepage&userid='+userid+'&unitid='+unitid+'&year={$year}&month={$month}&week={$week}';
{literal}	
}
function showWorktimebox(worktimeid, month, week, day, worktime)
{
	form1.worktimeid.value = worktimeid;
	form1.month.value = month;
	form1.week.value = week;
	form1.day.value = day;
	form1.val.value = worktime;	
	var obj = document.getElementById('bgworktimebox');
    obj.style.visibility = 'visible';
//	obj.style.width = document.body.offsetWidth+"px";
//	obj.style.height = document.body.offsetHeight+"px";
	
		
	obj = document.getElementById('worktimebox');
	obj.style.left = document.body.offsetWidth/2-100+"px";
	obj.style.top = document.body.offsetHeight/2-60+"px";
    obj.style.visibility = 'visible';
	form1.val.focus();
}
function hideWorktimebox()
{
	var obj = document.getElementById('bgworktimebox');
    obj.style.visibility = 'hidden';
	obj = document.getElementById('worktimebox');
    obj.style.visibility = 'hidden';
}
</script>
{/literal}
<table width="640" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">Arbetstid</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="427"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
				<td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
				<center><b>{$message}</b></center>
				{include file='Grund/errortext.tpl'}				
{foreach name=outer item=item from=$parentunits}
				<a href="sida.php?page=worktime&action=editworktimepage&unitid={$item.unitid}&year={$year}&month={$month}&week={$week}">{$item.unitname}</a> &gt;
{/foreach}				
				{if $unitid ne ''}
				{$unitname}
				{/if}
				{if $worktimeuserid ne ''}
				{$worktimename}
				{/if}				
{if $sizesubobjects gt 0}
				<select name="subunits" class="form" onChange="showWorktime(this.value);">
					<option value="0">- Ingen vald -</option>
{foreach name=outer item=item from=$subunits}
					<option value="unit{$item.unitid}">{$item.unitname}</option>	
{/foreach}
{foreach name=outer item=item from=$subusers}
					<option value="user{$item.userid}">{$item.name}</option>	
{/foreach}
				</select>
{/if}
				<table border="0" cellpadding="7px" cellspacing="0px">
				<tr>
				<td align="right">År {$year}</td>
				</tr>
{if $timeunit eq 'month'}
				<tr>
{foreach name=outer item=item from=$months}
				<td bgcolor="{cycle values="#d0d0d0,#FFFFFF"}" width="80px" align="center">{$item.monthname}<br/>
				{if $editable eq '1'}
				<a href="javascript:showWorktimebox('{if $item.userid eq $worktimeuserid or $item.unitid eq $unitid}{$item.worktimeid}{/if}', '{$item.month}', '0', '0', '{$item.worktime}');">{$item.worktime}</a>				
				{else}
				{$item.worktime}
				{/if}
				
				</td>
{/foreach}
				</tr>
{/if}

{if $timeunit eq 'week'}
				<tr>
				<td align="right"></td></tr>
		<tr><td>Vecka</td>
{foreach name=outer item=item from=$weeks}
	<td bgcolor="{cycle values="#d0d0d0,#FFFFFF"}">{$item.week}<br>
	{if $editable eq '1'}
	<a href="javascript:showWorktimebox('{if $item.userid eq $worktimeuserid or $item.unitid eq $unitid}{$item.worktimeid}{/if}', '0', '{$item.week}', '0', '{$item.worktime}');">{$item.worktime}</a>
    {else}
    {$item.worktime}
    {/if}
   </td>
	

	{if $item.week eq 14 || $item.week eq 28 || $item.week eq 42}	
				</tr>
		<tr><td>Vecka</td>
	{/if}			
{/foreach}
{/if}

{if $timeunit eq 'day'}
				<tr>
				<td align="right">{$monthname}</td>
{foreach name=outer item=item from=$days}
	{cycle values="#d0d0d0,#FFFFFF" reset="true" print=0}
	<td bgcolor="{cycle values="#d0d0d0,#FFFFFF"}">{$item.day}<br/>{$item.weekday}<br>
	{if $editable eq '1'}
	<a href="javascript:showWorktimebox('{if $item.userid eq $worktimeuserid or $item.unitid eq $unitid}{$item.worktimeid}{/if}', '{$item.month}', '0', '{$item.day_in_month}', '{$item.worktime}');">{$item.worktime}</a>
    {else}
    {$item.worktime}
    {/if}	
	</td>
	{if $item.weekday eq 'Sö'}	
				</tr>
		<tr><td></td>
	{/if}			
{/foreach}
{/if}


				</table>
				<br/>
				<a href="{$prevlink}">&lt;&lt; Föregående period</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="{$nextlink}">Nästa period &gt;&gt;</a>
                </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="3"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
<div style="background-color:#CCCCCC;width:100%; height:100%;top:0px;left:0px;position:absolute;filter:Alpha(opacity=60);visibility:hidden;" id="bgworktimebox"></div>

{include file='Grund/boxhead.tpl' boxtitle='Ändra arbetstid' boxtop='50' boxwidth='300' boxheight='200' divname='worktimebox'}
<table width="150px" height="100%" align="center"><tr><td valign="middle" align="left">
Ändra arbetstid:<br/>
<form name="form1">
<input type="hidden" name="worktimeid">
<input type="hidden" name="month">
<input type="hidden" name="day">
<input type="hidden" name="week">
<input type="text" name="val" value="0" size="10" class="form"><br/>
<input type="button" onClick="addWorktime(form1.worktimeid.value, '{$worktimeuserid}', '{$unitid}', '{$year}', form1.month.value, form1.week.value, form1.day.value, form1.val.value);" value="Ändra"> <input type="button" onClick="hideWorktimebox();" value="Avbryt"> 
</form>
</tr></td></table>
{include file='Grund/boxtail.tpl'}