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
<!-- indata to template boxtop, boxwidth, boxheight, divname, boxtitle -->
<div style="visibility:hidden; position:absolute; top:{$boxtop}px; z-index:99; left:100px; width:{$boxwidth}px; height:{$boxheight}px;  " name="{$divname}" id="{$divname}">

<table width="{$boxwidth}" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed; ">
            <tr>
                <td class="table_upperleftcorner_header1" width="23px"></td>
                <td class="table_uppermiddle_header1" nowrap width="120px"><p class="header">{$boxtitle}</p></td>
                <td class="table_upperend_header1" width="20px"></td>
                <td class="table_uppermiddle">&nbsp;</td>
                <td class="table_uppermiddle">&nbsp;</td>	
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
				<td style="background-color:#FFFFFF; " colspan="4">

