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
{*
 * Revisions:
 * #1.0.01  Markus Svensson       Changed to use the unitname of the headunit
 *}
<table width="640" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">{if $createuser eq '1'}Lägg till användare{/if}{if $updateuser eq '1' or  $updateuser eq '2' or  $updateuser eq '3'}Ändra användare{/if}</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="427"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td height="280" colspan="3" bgcolor="#FFFFFF" valign="top">
            <center><b>{$message}</b></center>
            {include file='Grund/errortext.tpl'}            
{if $updateuser eq '1'}            
{foreach name=outer item=item from=$parentunits}
            <a href="sida.php?page=unit&action=editunitpage&unitid={$item.unitid}">{$item.unitname}</a> &gt;
{/foreach}            
            <a href="sida.php?page=unit&action=editunitpage&unitid={$headunitunitid}">{$headunitunitname}</a>
{/if}            
            <table border="0" cellpadding="5" cellspacing="">
            <form name="form1" method="post" action="sida.php?page=user&action={if $createuser eq '1'}createuser{/if}{if $updateuser eq '1' or  $updateuser eq '2' or  $updateuser eq '3'}edituser{/if}">
            <input type="hidden" name="userid" value="{$displayeduserid}">
            <tr><td align="right">Användarnamn:</td><td>{if $updateuser eq '1' or $createuser eq '1'}<input type="text" class="form" name="username" size="10" value="{$displayedusername}">{elseif $updateuser eq '2' or $updateuser eq '3'}{$displayedusername}{/if}</td></tr>
            <tr><td align="right">Lösenord:</td><td>{if $updateuser eq '1' or $updateuser eq '2' or $createuser eq '1'}<input type="password" class="form" name="password" size="10" value="{$displayedpassword}">{elseif $updateuser eq '3'}******{/if}</td></tr>            
            <tr><td align="right">Namn:</td><td>{if $updateuser eq '1' or $createuser eq '1'}<input type="text" class="form" name="name" size="20" value="{$displayedname}">{elseif $updateuser eq '2' or $updateuser eq '3'}{$displayedname}{/if}</td></tr>
            <tr><td align="right">E-post:</td><td>{if $updateuser eq '1' or $updateuser eq '2' or $createuser eq '1'}<input type="text" class="form" name="email" size="20" value="{$displayedemail}">{elseif $updateuser eq '3'}{$displayedemail}{/if}</td></tr>            
            <tr><td align="right">Telefon:</td><td>{if $updateuser eq '1' or $updateuser eq '2' or $createuser eq '1'}<input type="text" class="form" name="phonenumber" size="20" value="{$displayedphonenumber}">{elseif $updateuser eq '3'}{$displayedphonenumber}{/if}</td></tr>            
            <tr><td align="right">Färg:</td><td>
            <select name="color" class="form" {if $updateuser ne '1' and $updateuser ne '2' and $createuser ne '1'}disabled{/if}>
{foreach name=outer item=item from=$colors}            
               <option value="{$item.color}" style="background-color:#{$item.color}" {if $item.color eq $displayedcolor}SELECTED{/if}>{$item.color}</option>
{/foreach}               
            </select>
            </td></tr>
            <tr><td align="right">Resurstyp:</td><td>
            <select name="resourceid" class="form" {if $updateuser ne '1' and $updateuser ne '2' and $createuser ne '1'}disabled{/if}>
               <option value="null">Ej definerad</option>            
{foreach name=outer item=item from=$resources}            
               <option value="{$item.resourceid}" {if $item.resourceid eq $displayedresourceid}SELECTED{/if}>{$item.resourcename}</option>
{/foreach}               
            </select>
            </td></tr>
            <tr><td align="right">Huvudenhet:</td><td>         
{if $headunitunitid eq ''}
               <input type="hidden" name="head_unitid" value="{$unitid}">{$unitname}
{else}
{*            <select name="head_unitid" class="form" {if $updateuser ne '1' and $createuser ne '1'}disabled{/if}>
{foreach name=outer item=item from=$createunits}
               <option value="{$item.unitid}" {if $item.unitid eq $displayedhead_unitid}SELECTED{/if}>{$item.unitname}</option>
{/foreach}
            </select>*}
            <input type="hidden" value="{$headunitunitid}" name="head_unitid">{$headunitunitname}
{/if}   
            </td></tr>

            <tr><td align="right">Intern resurs:</td>
            <td><input type="checkbox" value="1" name="internal" {if $displayedinternal ne 0}checked{/if} {if $updateuser ne '1' and $createuser ne '1'}disabled{/if}></td></tr>
            <tr><td align="right">Standardmodul:</td><td>
            <select name="default_moduleid" class="form" {if $updateuser ne '1' and $updateuser ne '2'  and $createuser ne '1'}disabled{/if}>
{foreach name=outer item=item from=$modules}
               <option value="{$item.moduleid}" {if $item.moduleid eq $displayeddefault_moduleid}SELECTED{/if}>{$item.modulename}</option>
{/foreach}
            </select></td></tr>
            <tr><td align="right">Rättigheter:</td><td></td></tr>
            <tr><td align="right"><input type="checkbox" class="form" name="locked" value="1" size="1" {if $displayedlocked eq '1'}checked{/if} {if $updateuser ne '1' and $createuser ne '1'}disabled{/if}></td><td>Användarens konto är låst</td></tr>                                                
            <tr><td align="right"><input type="checkbox" class="form" name="isadmin" value="1" size="1" {if $displayedisadmin eq '1'}checked{/if} {if $updateuser ne '1' and $createuser ne '1'}disabled{/if}></td><td>Administratör</td></tr>
            <tr><td align="right"><input type="checkbox" class="form" name="editdata" value="1" size="1" {if $displayededitdata eq '1'}checked{/if} {if $updateuser ne '1' and $createuser ne '1'}disabled{/if}></td><td>Får ändra personliga data</td></tr>            
<!--{foreach name=outer item=item from=$displayeduserrights}
            {if $item.inheadunit eq '1'}
            <tr><td align="right"><input type="checkbox" class="form" name="userright[]" value="{$item.rightname}" size="1" {if $item.is_set eq '1'}checked{/if} {if $updateuser ne '1'}disabled{/if}></td><td>{$item.shortname}</td></tr>                        
            {/if}
{/foreach}         -->
            <tr><td align="right">Rättigheter i enhet {if $updateuser eq '1'}{$unitname}{else}huvudenhet{/if}:</td><td>
            {if $updateuser eq '1' or $updateuser eq '2' or $updateuser eq '3'}
               <select onChange="location.href='sida.php?page=user&action=edituserpage&userid={$displayeduserid}&unitid='+this.value" name="otherunitid">
            <option value={$displayedhead_unitid} {if $displayedhead_unitid eq $unitid}SELECTED{/if}>{$headunitunitname}</option>   
{foreach name=outer item=item from=$displayedotherunits}
            <option value={$item.unitid} {if $item.unitid eq $unitid}SELECTED{/if}>{$item.unitname}</option>
{/foreach}
            </select>
            {/if}
            </td></tr>                  
            <input type="hidden" name="otherunitid" value="{$unitid}">      
{foreach name=outer item=item from=$displayeduserrights}
            <tr><td align="right"><input type="checkbox" class="form"  name="userright[]" value="{$item.rightname}" size="1"{if $item.setval eq '1'}checked{/if} {if $updateuser ne '1' and $updateuser ne '3'  and $createuser ne '1' }disabled{/if}></td><td>{$item.shortname}</td></tr>
{/foreach}               
{if $updateuser eq '1' or $updateuser eq '2' or $updateuser eq '3' or $createuser eq '1'}      
<tr><td></td><td><input type="image" src="Images/button_spara.png" width="60" height="20"></td></tr>
</form>
{/if}
{if $updateuser eq '1' or $updateuser eq '2'}
<tr><td colspan="2"><hr></td></tr>
<tr><td align="right">Övriga enheter:</td><td>
{foreach name=outer item=item from=$displayedotherunits}
            {$item.unitname} {if $updateuser ne '2'}(<a href="sida.php?page=user&action=removeotherunit&unitid={$item.unitid}&userid={$displayeduserid}">Ta bort</a>){/if}
            </td></tr><tr><td></td><td>
{/foreach}         
{if $updateuser ne '2'}   
            <form name="form2" action="sida.php" method="get">
            <input type="hidden" name="page" value="user">
            <input type="hidden" name="action" value="addotherunit">
            <input type="hidden" name="userid" value="{$displayeduserid}">
            <select name="unitid">
{foreach name=outer item=item from=$allunits}
{if $item.unitid ne $displayedhead_unitid}
               <option value="{$item.unitid}">{$item.unitname}</option>
{/if}
{/foreach}            
            </select> <input type="image" src="Images/button_spara.png" width="60" height="20">
            </form>
{/if}            
            </td></tr>
{/if}      
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
