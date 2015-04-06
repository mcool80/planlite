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
{literal}
<script>
function showPlannedtimebox(activitytimeid, resourceplannedtimeid, startdate, plannedtime, workedtime)
{
	form3.activitytimeid.value = activitytimeid;
	form3.resourceplannedtimeid.value = resourceplannedtimeid;
	form3.startdate.value = startdate;
	form3.plannedtime.value = plannedtime;
	form3.workedtime.value = workedtime;

	var obj = document.getElementById('bgactivityslotbox');
    obj.style.visibility = 'visible';
//	obj.style.width = document.body.offsetWidth+"px";
//	obj.style.height = document.body.offsetHeight+"px";
		
	obj = document.getElementById('editplannedtimebox');
	obj.style.left = document.body.offsetWidth/2-100+"px";
	obj.style.top = document.body.offsetHeight/2-60+"px";
    obj.style.visibility = 'visible';
}
function hidePlannedtimebox()
{
	var obj = document.getElementById('bgactivityslotbox');
    obj.style.visibility = 'hidden';
	obj = document.getElementById('editplannedtimebox');
    obj.style.visibility = 'hidden';
}
function addPlannedtime(activitytimeid, resourceplannedtimeid, startdate, plannedtime, workedtime)
{
	location.href = 'sida.php?page=activity&action=edituserplanned&activityid={/literal}{$activityid}{literal}&activitytimeid='+activitytimeid+'&resourceplannedtimeid='+resourceplannedtimeid+'&startdate='+startdate+'&plannedtime='+plannedtime+'&workedtime='+workedtime;
}

function showActivityslotbox(activityslotid, plannedtime)
{
	form4.activityslotid.value = activityslotid;
	form4.plannedtime.value = plannedtime;

	var obj = document.getElementById('bgactivityslotbox');
    obj.style.visibility = 'visible';
	obj.style.width = document.body.offsetWidth+"px";
	obj.style.height = document.body.offsetHeight+"px";
		
	obj = document.getElementById('editactivityslotbox');
	obj.style.left = document.body.offsetWidth/2-100+"px";
	obj.style.top = document.body.offsetHeight/2-60+"px";
    obj.style.visibility = 'visible';
}
function hideActivityslotbox()
{
	var obj = document.getElementById('bgactivityslotbox');
    obj.style.visibility = 'hidden';
	obj = document.getElementById('editactivityslotbox');
    obj.style.visibility = 'hidden';
}
function addActivityslot(activityslotid, plannedtime)
{
	location.href = 'sida.php?page=activity&action=editactivityslot&activityid={/literal}{$activityid}{literal}&activityslotid='+activityslotid+'&plannedtime='+plannedtime;
}
</script>
{/literal}
<script language="JavaScript" src="Include/date-picker.js"></script>
<table width="640" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">Ändra aktivitet</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="180"></td>
                <td class="table_uppermiddle" width="247"></td>	
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
				<td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
				<center><b>{$message}</b></center>
				{include file='Grund/errortext.tpl'}
				<table border="0" cellpadding="5" cellspacing="">

				<form name="form1" method="post" action="sida.php?page=activity&action=editactivity">
				<input type="hidden" name="activityid" value="{$activityid}">
				<tr><td align="right">Enhet:</td><td>
				<select name="unitid" class="form">
{foreach name=outer item=item from=$createunits}
					<option value="{$item.unitid}" {if $item.unitid eq $unitid}SELECTED{/if}>{$item.unitname}</option>
{/foreach}
				</select></td></tr>				
				<tr><td align="right">Aktivitetnamn:</td><td><input type="text" class="form" name="activityname" size="10" value="{$activityname}"></td></tr>
				<tr><td align="right">Beskrivning:</td><td><input type="text" class="form" name="description" size="20" value="{$description}"></td></tr>
				<tr><td align="right">Interntid:</td><td><input type="checkbox" name="isinternaltime" value="1" {if $isinternaltime eq '1'}checked{/if}></td></tr>
				<tr><td align="right">Kostnadsbärare:</td><td><input type="text" class="form" name="costdriver" size="13" value="{$costdriver}"> </td></tr>
				<tr><td align="right">Påminnelse</td><td><input type="text" class="form" name="notifytime" size="3" value="{$notifytime}"> timmar innan aktiviet startar</td></tr>
				<tr><td colspan="2" align="right"><input type="image" src="Images/button_spara.png" width="60" height="20"></td></tr>
				<tr><td align="right">Statistik:</td><td></td></tr>
				</form>
				</table>
                </td>
				<td height="280" bgcolor="#FFFFFF" valign="top">
				<table border="0" cellpadding="5" cellspacing="" style="border-left-style:dashed; border-left-color:#000000; border-left-width:1px ">
					<tr><td><b>Tillfällen</b></td></tr>
{foreach name=outer item=item from=$activityslots}
					<tr><td>{$item.startdate} - {$item.stopdate} (<a href="sida.php?page=activity&action=removeactivityslot&activityslotid={$item.activityslotid}" onClick="if (!confirm('Vill du ta bort planerad tid?')) return false;">Ta bort</a>)<br/>
							Planerad tid: <a href="javascript:showActivityslotbox('{$item.activityslotid}', '{$item.plannedtime}');">{$item.plannedtime}</a></td></tr>
					<tr><td><a href="javascript:data{$item.activityslotid}.style.visibility='visible';" >Detaljerad information &gt;&gt;</a><br/>
					<div id="data{$item.activityslotid}">
					<table>
{foreach name=outer item=item2 from=$item.userplannedtime}				
					<tr><td>{$item2.name}<br/>
					<a href=    "javascript:showPlannedtimebox('{$item2.activitytimeid}', '{$item2.resourceplannedtimeid}', '{$item2.startdate}', '{$item2.plannedtime}', '{$item2.workedtime}');">{$item2.startdate}</a></td>
					<td><a href="javascript:showPlannedtimebox('{$item2.activitytimeid}', '{$item2.resourceplannedtimeid}', '{$item2.startdate}', '{$item2.plannedtime}', '{$item2.workedtime}');">{$item2.plannedtime}</a></td>
					<td><a href="javascript:showPlannedtimebox('{$item2.activitytimeid}', '{$item2.resourceplannedtimeid}', '{$item2.startdate}', '{$item2.plannedtime}', '{$item2.workedtime}');">{$item2.workedtime}</a></td>
					<td>(<a href="?page=activity&action=removeuserplanned&activitytimeid={$item2.activitytimeid}&activityid={$activityid}&resourceplannedtimeid={$item2.resourceplannedtimeid}" onClick="if (!confirm('Vill du ta bort planerad tid?')) return false;">Ta bort</a>)</td></tr>
{/foreach}
					</table>
					<select name="users{$item.activityslotid}" class="form">
{foreach name=outer item=item2 from=$users}
					<option value="{$item2.userid}">{$item2.name}</option>
{/foreach}
					</select> 
					<input type="button" value="L&auml;gg till" onClick="location.href='sida.php?page=activity&action=adduserplanned&activityslotid={$item.activityslotid}&activityid={$activityid}&userid='+users{$item.activityslotid}.value+'&plannedtime=0'">
					<br>
					<select name="resources{$item.activityslotid}" class="form">
{foreach name=outer item=item2 from=$resources}
					<option value="{$item2.resourceid}">{$item2.resourcename}</option>
{/foreach}
					</select> 
					<input type="button" value="L&auml;gg till" onClick="location.href='sida.php?page=activity&action=adduserplanned&activityslotid={$item.activityslotid}&activityid={$activityid}&resourceid='+resources{$item.activityslotid}.value+'&plannedtime=0'">					<br/>
					</div>
					<hr/></td></tr>
{/foreach}
					<tr><td><b>Nytt tillfälle:</b></td></tr>
					<form name="form2" action="sida.php?page=activity&action=addactivityslot" method="post">
					<input type="hidden" name="activityid" value="{$activityid}">
					<tr><td>Startdatum:<br/>
					<input type="text" class="form" name="startdate" size="10"> <input type="text" class="form" name="starthour" size="1" value="0" style="text-align:right">:<input type="text" class="form" name="startmin" size="1" value="00"> <a href="javascript:show_calendar('form2.startdate');"><img src="Images/button_kalender.png" border="0"></a><br/>
					</td></tr>
					<tr><td>Slutdatum:<br/>
					<input type="text" class="form" name="stopdate" size="10"> <input type="text" class="form" name="stophour" size="1" value="0" style="text-align:right">:<input type="text" class="form" name="stopmin" size="1" value="00"> <a href="javascript:show_calendar('form2.stopdate');"><img src="Images/button_kalender.png" border="0"> </a><br/>
					 </td></tr>					
					<tr><td>Planerad tid:<br/>
					<input type="text" size="5" name="plannedtime" value="0"></td></tr>
					<tr><td align="right"><input type="image" src="Images/button_spara.png" width="60" height="20"></td></tr>
					</form>
					</table>
				</td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="4"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
<div style="background-color:#CCCCCC;width:100%;height:100%;top:0px;left:0px;position:absolute;filter:Alpha(opacity=60);visibility:hidden;" id="bgactivityslotbox"></div>

{include file='Grund/boxhead.tpl' boxtitle='Ändra planerad tid' boxtop='20' boxwidth='300' boxheight='200' divname='editplannedtimebox'}

<table width="250px" height="160px" align="center" cellspacing="2px" cellpadding="0px">
<form name="form3">
<input type="hidden" name="activitytimeid">
<input type="hidden" name="resourceplannedtimeid">
<tr><td>Datum:</td><td><input type="text" name="startdate" size="16" class="form"> <a href="javascript:show_calendar('form3.startdate');"></a></td></tr>
<tr><td>Planerad tid:</td><td><input type="text" name="plannedtime" value="0" size="10" class="form"></td></tr>
<tr><td>Arbetad tid:</td><td><input type="text" name="workedtime" value="0" size="10" class="form"></td></tr>
<tr><td colspan="2" align="right"><input type="button" onClick="addPlannedtime(form3.activitytimeid.value, form3.resourceplannedtimeid.value, form3.startdate.value, form3.plannedtime.value, form3.workedtime.value);" value="Ändra"> <input type="button" onClick="hidePlannedtimebox();" value="Avbryt"> </td></tr>
</form>
</table>
{include file='Grund/boxtail.tpl'}


{include file='Grund/boxhead.tpl' boxtitle='Ändra planerad tid' boxtop='50' boxwidth='300' boxheight='200' divname='editactivityslotbox'}
<table width="150px" height="100%" align="center"><tr><td valign="middle" align="left">
<form name="form4">
<input type="hidden" name="activityslotid">
<input type="text" name="plannedtime" value="0" size="10" class="form"><br/>
<input type="button" onClick="addActivityslot(form4.activityslotid.value, form4.plannedtime.value);" value="Ändra"> <input type="button" onClick="hideActivityslotbox();" value="Avbryt"> 
</form>
</tr></td></table>
{include file='Grund/boxtail.tpl'}
