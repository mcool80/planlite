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
<table width="640" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">{if $installmodule eq '1'}Installera modul{/if}</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="427"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
				{include file='Grund/errortext.tpl'}				
{if $installmodule eq '1'}			
				<table border="0" cellpadding="5" cellspacing="">
				<form name="form1" action="?page=module&action=installmodule" method="post" enctype="multipart/form-data">
				<input type="hidden" name="organisationid" value="{$organisationid}">
				<tr><td align="right">Modulefile:</td><td><input type="file" class="form" name="filename" size="20" ></td></tr>
				</td></tr>
				<tr><td colspan="2" align="right"><input type="image" src="Images/button_spara.png" width="60" height="20"></td></tr>
				</form>
				</table>
{/if}
{if $installmodule2 eq '1'}
				<table border="0" cellpadding="5" cellspacing="">
				<tr><td>
				<h1>Modul installerad</h1>
				<b>Modulnamn: </b>{$modulename}<br>
				<b>Version: </b>{$version}<br>
				<br/>
				Information från uppackning från fil:<br/>
				{$unzipinfo}				
				</td></tr>
				</table>
{/if}				
                </td>
	            <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="3"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
