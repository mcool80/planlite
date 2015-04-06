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
                <td class="table_uppermiddle_header1" nowrap><p class="header">{if $createorganisation eq '1'}Lägg till organisation{else}&Auml;ndra organisation{/if}</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="427"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
				<b>{$message}</b>
				{include file='Grund/errortext.tpl'}				
				<table border="0" cellpadding="5" cellspacing="">
				<form action="sida.php?page=organisation&action=createorganisation" method="post" name="form1">
				<input type="hidden" name="organisationid" value="{$organisationid}">
				<tr><td align="right">Namn:</td><td><input type="text" class="form" name="organisationname" size="30" value="{$organisationname}"></td></tr>
				<tr><td align="right">Beskrivning:</td><td><input type="text" class="form" name="description" size="50" value="{$description}"></td></tr>
				{if $createorganisation eq '1'}
				<tr>
				   <td align="right">Namn p&aring; administratör:</td>
				   <td><input type="text" name="admin" class="form1" size="10">
				<tr>
				   <td align="right">L&ouml;senord f&ouml;r administratör:</td>
				   <td><input type="password" name="password" class="form1" size="10"></td></tr>
				{/if}
				<tr><td align="right">Antal tillåtna användare:</td><td>
				<select name="no_users">
				<option value="5" {if $no_users eq '5'}SELECTED{/if}>5</option>
				<option value="10" {if $no_users eq '10'}SELECTED{/if}>10</option>
				<option value="15" {if $no_users eq '15'}SELECTED{/if}>15</option>
				<option value="20" {if $no_users eq '20'}SELECTED{/if}>20</option>
				<option value="50" {if $no_users eq '50'}SELECTED{/if}>50</option>
				<option value="100" {if $no_users eq '100'}SELECTED{/if}>100</option>
				<option value="200" {if $no_users eq '200'}SELECTED{/if}>200</option>
				<option value="999999" {if $no_users eq '999999'}SELECTED{/if}>>200</option>
				</select></td></tr>				
				<tr><td align="right">Tillåtna moduler:</td><td>
				<select name="moduleid" size="5" multiple="true">
				
{foreach name=outer item=item from=$modules}
					<option value="{$item.moduleid}"
{foreach name=outer item=org from=$orgmodules}{if $item.moduleid eq $org.moduleid}SELECTED{/if}{/foreach} >{$item.modulename}</option>
{/foreach}
				</select>
				(minst en modul m&aring;ste v&auml;ljas) </td>
				</tr>
				<tr><td align="right">Adress:</td><td><textarea class="form" name="address">{$address}</textarea></td></tr>				
				<tr><td align="right">Postnummer:</td><td><input type="text" class="form" name="zipcode" size="5" value="{$zipcode}"></td></tr>				
				<tr><td align="right">Postort:</td><td><input type="text" class="form" name="city" size="15" value="{$city}"></td></tr>				
				<tr><td align="right">Telefon:</td><td><textarea class="form" name="phonenumbers">{$phonenumbers}</textarea></td></tr>	
				<tr>
				   <td align="right">Kontaktperson(namn):</td>
				   <td><input type="text" class="form" name="contact" size="25" value="{$contact}"></td></tr>										
				<tr><td align="right">Typ av organisation:</td><td>
				<select name="organisationtypeid">
{foreach name=outer item=item from=$orgtype}
					<option value="{$item.organisationtypeid}" {if $item.organisationtypeid eq $organisationtypeid}SELECTED{/if}>{$item.typename}</option>
{/foreach}
				</select></td></tr>			
{if $createorganisation ne '1'}					
				<tr><td align="right">Relaterade organisationer:</td><td>
{foreach name=outer item=item from=$relatedorganisation}
				{$item.organisationname} <a href="sida.php?page=organisation&action=removerelatedorganistion&organisationid={$organisationid}&relatedorganisationid={$item.organisationid}">Ta bort</a><br/>
{/foreach}
				</td></tr>
				<tr><td></td><td>
				<select name="addrelorg">
				{foreach name=outer item=item from=$organisations}
				{if $item.organisationid ne $organisationid}
				<option value="{$item.organisationid}">{$item.organisationname}</option>
				{/if}
				{/foreach}
				</select>
				<input type="button" value="L&auml;gg till" onclick="location.href='sida.php?page=organisation&action=addrelatedorganisation&organisationid={$organisationid}&relatedorganisationid='+form.addrelorg.value;">
				</td></tr>
{/if}				
				<tr><td colspan="2" align="right"><input type="image" src="Images/button_spara.png" width="60" height="20"></td></tr>
				</table>
</form>				
                </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="3"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
