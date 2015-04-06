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
                <td class="table_uppermiddle_header1" nowrap><p class="header">Aktiviteter</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="427"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
				<td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
				<center><b>{$message}</b></center>
				{include file='Grund/errortext.tpl'}				
				<table border="0" cellpadding="5" cellspacing="">
{foreach name=outer item=item from=$activitys}
				<tr><td>{$item.activityname} (<a href="sida.php?page=activity&action=editactivitypage&activityid={$item.activityid}">Ändra</a>) (<a href="sida.php?page=activity&action=removeactivity&activityid={$item.activityid}" onClick="if (!confirm('Vill du ta bort aktivitet {$item.activityname}?')) return false;">Ta bort</a>)</td></tr>
{/foreach}
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
