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
 * #1.0.01  Markus Svensson       Added title to helptext, and changed to user objects in template
 *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>PlanLite</title>
    <link rel="stylesheet" href="Include/planlite.css" type="text/css">
</head>
<body style="margin:0px; ">
<table width="640" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed">
            <tr>
                <td class="table_upperleftcorner_header1" width="20px"></td>
                <td class="table_uppermiddle_header1" nowrap width="100px"><p class="header">Hjälpen</p></td>
                <td class="table_upperend_header1" width="23px"></td>
                <td class="table_uppermiddle">&nbsp;</td>
                <td class="table_uppermiddle">&nbsp;</td>   
                <td class="table_upperrightcorner_header1" width="20px"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
            <td height="280" colspan="4" bgcolor="#FFFFFF" valign="top">
            <p><b>{$help->title}</b></p>
            {foreach  name=outer item=item from=$helptexts}
               <a href="#{$item->subpagename}">{$item->title}</a> |
            {/foreach}
            <hr/>
            {foreach  name=outer item=item from=$helptexts}
               <a name="{$item->subpagename}" />
               <p><b>{$item->title}</b></p>
               <p>{$item->text}</p>
               <hr/>
            {/foreach}
            </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="4"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
</body>
</html>