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
    <title>PlanLite | Fel</title>
    <link rel="stylesheet" href="Include/planlite.css" type="text/css">
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" width="520" height="470" align="center">
<tr>
    
    <td valign="top">
    
        <!-- Sidans huvudinnehåll -->
        <table width="680" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">Fel i Planlite </p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="480"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td colspan="3" bgcolor="#FFFFFF" valign="top" height="115" align="left">

                    <br>
					<h1>{$errorheader}</h1>
					<p><b>{$errormsg}</b></p>
{foreach name=outer item=item from=$backtrace}
			{$item.class}::{$item.function} line {$item.line}<br/>
{/foreach}		
					<p><i>(Felkoden för felet är {$errorid}, ange denna till support om inte felet löser sig)</i></p>
					<p><a href="javascript:history.go(-1);">&lt;&lt;Tillbaka</a></p>
                </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="3"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
        
    </td>
</tr>
</table>

</body>
</html>
