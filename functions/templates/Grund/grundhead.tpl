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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>PlanLite</title>
    <link rel="stylesheet" href="Include/planlite.css" type="text/css">
</head>
<form name="status">
<input type="hidden" value="0" name="changed">
</form>
<body onbeforeunload="if ( status.changed.value!='0' ) 
//               if ( !confirm('Vill du lämna sidan utan att spara?') )
                  return 'Vill du lämna sidan utan att spara?';
               ">

<table border="0" cellspacing="0" cellpadding="0" width="800" height="470" align="center">
<tr>
    <td valign="top">

        <!-- Logo -->
        <table width="190" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner"></td>
                <td class="table_uppermiddle"></td>
                <td class="table_upperrightcorner"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td bgcolor="#FFFFFF" height="80" valign="top" align="center">
                    <img src="Images/logo.png">
                </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td bgcolor="#FFFFFF" valign="bottom">
                    Välkommen<br>
                    {$name}
                </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
    
        <!-- Modul -->
      {if $showmodule ne '0'}
<!--        <table width="190" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header2"></td>
                <td class="table_uppermiddle_header2"><p class="header">Välj modul</p></td>
                <td class="table_upperrightcorner_header2"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td bgcolor="#FFFFFF">
                
                <form class="nomargin">
                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td height="15"></td>  
                    </tr>
                    <tr>
                        <td>
                            <select class="form" name="modules">
                     {foreach name=outer item=item from=$modules}
                                <option onclick="href.location='{$item.defaultpagename}';" value="{$item.defaultpagename}">{$item.modulename}</option>
                     {/foreach}
                            </select><br>
                     {$currentpagename} - {$currentsubpagename}
                        </td>
                        <td width="8"></td> 
                        <td>
                            <input  class="button" type="image" src="Images/button_ok.png" width="35" height="20" onclick="location.href=modules.">
                        </td>
                    </tr>
                    <tr>
                        <td height="5"></td>  
                    </tr>
                    </table>    
                </form>
                        
                </td>
                <td class="table_rightmiddle_header2"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle"></td>
                <td class="table_lowerrightcorner_header2"></td>
            </tr>
        </table>
        {/if}
-->
        <!-- Meny -->
      {if $showmeny ne '0'}
        <table width="190" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header2"></td>
                <td class="table_uppermiddle_header2"><p class="header">Huvudmeny</p></td>
                <td class="table_upperrightcorner_header2"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td bgcolor="#FFFFFF">
                    <p></p>
               {if $isappadmin eq '1'}
                    <p class="menu"><a href="sida.php?page=module&action=installmodulepage" class="menu">&nbsp;Installera modul&nbsp;</a></p>
                    <p class="menu"><a href="" class="menu">&nbsp;Uppgradera moduk&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=organisation&action=createorganisationpage" class="menu">&nbsp;Skapa organisation&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=organisation&action=showorganisations" class="menu">&nbsp;&Auml;ndra organisation&nbsp;</a></p>               
                    <p class="menu"><a href="sida.php?page=user&action=edituserpage&userid={$userid}" class="menu">&nbsp;Om mig&nbsp;</a></p>
               {elseif $isadmin eq '1'}
                    <p class="menu"><a href="sida.php?page=activity&action=createactivitypage" class="menu">&nbsp;Lägga till aktivitet&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=activity&action=showactivitys" class="menu">&nbsp;Ändra aktivitet&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=hiearki&action=showlist&showusers=false" class="menu">&nbsp;Uppdatera enhet&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=worktime&action=editworktimepage&userid={$userid}" class="menu">&nbsp;Ändra arbetstid&nbsp;</a></p>                              
               {else}
                    <p class="menu"><a href="sida.php?page=activity&action=createactivitypage" class="menu">&nbsp;Lägga till aktivitet&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=activity&action=showactivitys" class="menu">&nbsp;Ändra aktivitet&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=worktime&action=editworktimepage&userid={$userid}" class="menu">&nbsp;Ändra arbetstid&nbsp;</a></p>                              
                    <p class="menu"><a href="sida.php?page=user&action=edituserpage&userid={$userid}" class="menu">&nbsp;Om mig&nbsp;</a></p>
               {/if}
                    <p class="menu"><a href="#" onclick="window.open('sida.php?page=help&action=showhelp&pagename={$currentpagename}&subpagename={$currentsubpagename}', '_blank', 'status=0,resizeable=0,width=640,height=480,location=0,toolbar=0,menubar=0,directories=0');" class="menu">&nbsp;Hjälp&nbsp;</a></p>
                    <p class="menu"><a href="sida.php?page=login&action=logout" class="menu">&nbsp;Logga ut&nbsp;</a></p>
               
                </td>
                <td class="table_rightmiddle_header2"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle"></td>
                <td class="table_lowerrightcorner_header2"></td>
            </tr>
        </table>
       {/if}
    </td>
    <td valign="top">
  

                