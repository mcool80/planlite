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
<table width="640" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">Lägg till aktivitet</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="427"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
				<td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
				<center><b>{$message}</b></center>
				<table border="0" cellpadding="5" cellspacing="">

				
				<form name="form1" method="post" action="sida.php?page=activity&action=createactivity">
				<input type="hidden" name="unitid" value="{$unitid}">
				<tr><td align="right">Enhet:</td><td>
				<select name="unitid" class="form">
{foreach name=outer item=item from=$createunits}
					<option value="{$item.unitid}" {if $item.unitid eq $parentunitid}SELECTED{/if}>{$item.unitname}</option>
{/foreach}
				</select></td></tr>				
				<tr><td align="right">Aktivitetnamn:</td><td><input type="text" class="form" name="activityname" size="10" value="{$unitname}"></td></tr>
				<tr><td align="right">Beskrivning:</td><td><input type="text" class="form" name="description" size="20" value="{$description}"></td></tr>
				<tr><td align="right">Interntid:</td><td><input type="checkbox" name="isinternaltime" value="1"></td></tr>
				<tr><td align="right">Kostnadsbärare:</td><td><input type="text" class="form" name="costdriver" size="13"> </td></tr>
				<tr><td align="right">Startar:</td><td><input type="text" class="form" name="startdate" size="10"> <input type="text" class="form" name="starthour" size="1" value="0" style="text-align:right">:<input type="text" class="form" name="startmin" size="1" value="00"> <a href="javascript:show_calendar('form1.startdate');"><img src="Images/button_kalender.png" border="0"></a></td></tr>				
				<tr><td align="right">Slutar:</td><td><input type="text" class="form" name="stopdate" size="10"> <input type="text" class="form" name="stophour" size="1" value="0" style="text-align:right">:<input type="text" class="form" name="stopmin" size="1" value="00"> <a href="javascript:show_calendar('form1.stopdate');"><img src="Images/button_kalender.png" border="0"></a></td></tr>
				<tr><td align="right">Påminnelse</td><td><input type="text" class="form" name="notifytime" size="3"> timmar innan aktiviet startar</td></tr>
				<tr><td align="right">Planerad tid:</td><td><input type="text" class="form" name="plannedtime" size="4"> timmar</td></tr>	
				<tr><td align="right"></td><td></td></tr>
				<tr><td align="right">Upprepa:</td><td></td></tr>	
				<tr><td align="right">Hur ofta</td><td>
				<select name="repeat" class="form">
					<option value="daily">Dagligen</option>
					<option value="weekly">En gång per vecka</option>					
					<option value="monthly">En gång per månad</option>										
				</select>
				</td></tr>
				<tr><td align="right">Antal gånger</td><td><input type="text" class="form" name="count" size="4"> eller fram till datum <input type="text" class="form" name="enddate" size="10" onClick="">  <a href="javascript:show_calendar('form1.stopdate');"><img src="Images/button_kalender.png" border="0"></a></td></tr>
				<tr><td colspan="2" align="right"><input type="image" src="Images/button_spara.png" width="60" height="20"></td></tr>
				</form>
				</table>
                </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="3"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
