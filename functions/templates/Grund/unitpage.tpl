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
<table width="640" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed; ">
            <tr>
                <td class="table_upperleftcorner_header1" width="20px"></td>
                <td class="table_uppermiddle_header1" nowrap width="200px"><p class="header">{if $createunit eq '1'}Lägg till enhet{/if}{if $updateunit eq '1'}Ändra i {$unitname}{/if}</p></td>
                <td class="table_upperend_header1" width="23px"></td>
                <td class="table_uppermiddle">&nbsp;</td>
                <td class="table_upperrightcorner_header1">&nbsp;</td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
				<b>{$message}</b><br/>
				{include file='Grund/errortext.tpl'}				

{foreach name=outer item=item from=$parentunits}
				<a href="sida.php?page=unit&action=editunitpage&unitid={$item.unitid}">{$item.unitname}</a> &gt;
{/foreach}				
{if $createunit eq '1'}
				<i>Ny enhet</i>
{else}
				{$unitname}
{/if}
{if $createunitssize ne '0'}				
<!--{foreach name=outer item=item from=$createunits}
					{if $item.unitid eq $parentunitid}{$item.unitname}{/if}<!--option value="{$item.unitid}" {if $item.unitid eq $parentunitid}SELECTED{/if}>{$item.unitname}</option>
{/foreach}-->
{/if}
                <table border="0" cellpadding="0" cellspacing="0">
				<tr><td width="420px">
				<table border="0" cellpadding="5" cellspacing="">
				<form name="form1" method="post" action="sida.php?page=unit&action={if $createunit eq '1'}createunit{/if}{if $updateunit eq '1'}editunit{/if}">
				<input type="hidden" name="unitid" value="{$unitid}">
				<input type="hidden" name="parentunitid" value="{$parentunitid}">
				<tr><td colspan="2"><b>Information om enhet</b></td></tr>
				<tr><td align="right">Enhetsnamn:</td><td><input type="text" class="form" name="unitname" size="20" value="{$unitname}"></td></tr>
				<tr><td align="right">Beskrivning:</td><td><input type="text" class="form" name="description" size="30" value="{$description}"></td></tr>
				<tr><td align="right">Typ av schema:</td><td>
				<select name="unittypeid" class="form">
{foreach name=outer item=item from=$unittypes}				
					<option value="{$item.unittypeid}" {if $item.unittypeid eq $unittypeid}SELECTED{/if}>{$item.unittypename}</option>
{/foreach}					
				</select>
				</td></tr>
				<tr><td align="right">Minsta planeringstid:</td><td>
				<select name="planningtypeid"  class="form">
{foreach name=outer item=item from=$planningtypes}				
					<option value="{$item.planningtypeid}" {if $item.planningtypeid eq $planningtypeid}SELECTED{/if}>{$item.planningtypename}</option>
{/foreach}				
				</select></td></tr>			
				<tr><td align="right">&Auml;ndra aktivitet senast</td><td><input type="text" class="form" name="hour_limit" size="10" value="{$hour_limit}"> timmar f&ouml;re aktivitet startar.</td></tr>

<tr><td colspan="2" align="right"><input type="image" src="Images/button_spara.png" width="60" height="20"><br/><br/></td></tr>
				</form>
{if $updateunit eq '1'}
				<tr><td colspan="2"> <hr></td></tr>		
				<tr><td align="left" valign="top" colspan="2">Personer:
				<table border="0">
{foreach name=outer item=item from=$users}
					<tr><td width="250px">{$item.name} </td><td><a href="sida.php?page=user&action=edituserpage&userid={$item.userid}">Ändra</a> | <a href="?page=user&action=removeuser&userid={$item.userid}&returl=%3Fpage%3Dunit%26action%3Deditunitpage%26unitid%3D{$unitid}" onClick="if (!confirm('Vill du ta bort person {$item.name} från systemet?')) return false;">Ta bort</a></td></tr>
{/foreach}
{foreach name=outer item=item from=$otherusers}
					<tr><td width="250px">{$item.name} ({$item.unitname})</td><td><a href="sida.php?page=user&action=edituserpage&userid={$item.userid}&unitid={$unitid}">Ändra</a> | <a href="sida.php?page=user&action=removeotherunit&userid={$item.userid}&unitid={$unitid}&redirecturl=sida.php%3Fpage%3Dunit%26action%3Deditunitpage%26unitid%3D{$unitid}" onClick="if (!confirm('Vill du ta bort person {$item.name} från enhet?')) return false;">Ta bort</a></td></tr>
{/foreach}
				</table>
				<a href="sida.php?page=user&action=createuserpage&unitid={$unitid}">Lägg till person...</a>
				</td></tr>
						
<tr><td align="left" valign="top" colspan="2">Resurstyper:
<table border="0">
{foreach name=outer item=item from=$resources}
				<tr><td width="150px">
<div id="editres{$item.resourceid}" style="visibility:hidden; position:absolute">
				<form name="formres{$item.resourceid}" action="sida.php" method="get">
				<input type="hidden" name="page" value="unit">
				<input type="hidden" name="action" value="editresource">
				<input type="hidden" name="unitid" value="{$item.unitid}">
				<input type="hidden" name="resourceid" value="{$item.resourceid}">
				<input type="text" name="resourcename" value="{$item.resourcename}">				
				<input type="image" src="Images/button_spara.png" width="60" height="20">
				<a href="#" onClick="editres{$item.resourceid}.style.visibility='hidden';resname{$item.resourceid}.style.visibility='visible';">
				<img src="Images/button_avbryt.png" border="0"></a>
				</form>
</div>
<div id="resname{$item.resourceid}" style="visibility:visible; ">
					{$item.resourcename} </td><td><a href="#" onClick="editres{$item.resourceid}.style.visibility='visible';resname{$item.resourceid}.style.visibility='hidden';">Ändra</a> | <a href="sida.php?page=unit&action=removeresource&resourceid={$item.resourceid}" onClick="if (!confirm('Vill du ta bort resurstyp {$item.resourcename} från enhet?')) return false;">Ta bort</a></td></tr>
</div>
{/foreach}
</table>
				<form name="form2" action="sida.php" method="get">
				<input type="hidden" name="page" value="unit">
				<input type="hidden" name="action" value="addresource">
				<input type="hidden" name="unitid" value="{$unitid}">
				Ny resurstyp:<br>
				<input type="text" size="15" class="form" name="resourcename"> <input type="image" src="Images/button_spara.png" width="60" height="20">
				</form>
				</table>
				</td>					

<td style="border-left-width:thin; border-left-color:#000000; border-left-style:dotted; " valign="top" width="120px">
					<table border="0" cellspacing="5px">			
				<tr><td align="left" valign="top"><b>Informationstexter:</b><br/><br/>
{foreach name=outer item=item from=$informationtexts}
					{$item.startdate|date_format:"%Y-%m-%d"} - {$item.stopdate|date_format:"%Y-%m-%d"}<br/> 
					{$item.informationtext}<br/>
					(<a href="sida.php?page=unit&action=removeinformationtext&informationtextid={$item.informationtextid}" onClick="if (!confirm('Vill du ta bort informationstext från enhet?')) return false;">Ta bort</a>)
					<hr/>
{/foreach}				
				</td></tr>								
<tr><td valign="top">
				<form name="form3" action="sida.php" method="get">
				<input type="hidden" name="page" value="unit">
				<input type="hidden" name="action" value="addinformationtext">
				<input type="hidden" name="unitid" value="{$unitid}">
				Ny informationstext:<br/>
				<table border="0">
				<tr><td>Start:</td><td>
				<input type="text" class="form" size="15" onFocus="javascript:this.blur();show_calendar('form3.startdate');" name="startdate">
				</td></tr>
				<tr><td>Slut:</td><td>
				<input type="text" class="form" size="15" name="stopdate" onFocus="javascript:this.blur();show_calendar('form3.stopdate');">
				</td></tr>
				<tr><td valign="top">Text:</td><td>
				<textarea class="form" name="informationtext" cols="18" rows="6"></textarea><br/> <input type="image" src="Images/button_spara.png" width="60" height="20">
				</td></tr>				
				</table>
				</td></tr> 
				</form>
{/if}				
				</table>
				</td></tr>
			
				</table>

                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="3"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
