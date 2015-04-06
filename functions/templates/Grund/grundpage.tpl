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
        <!-- Sidans huvudinnehåll -->
		<table width="640" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><img src="Images/upperleftcorner_header1.png" width="20" height="35"></td>
                <td class="table_uppermiddle_header1" width="150" nowrap><p class="header">Information</p></td>
                <td class="table_uppermiddle">
                    <table border="0" cellpadding="0" cellspacing="0" width="450">
                    <tr>
                        <td><img src="Images/upperend_header1.png" width="23" height="35"></td>
                        <td></td>
                        <td align="right"><img src="Images/uppermiddle_flower.png" width="228" height="35"></td>
                    </tr>
                    </table>
               
                <td><img src="Images/upperrightcorner_flower.png" width="20" height="35"></td>
            </tr>
            <tr>
            <td colspan="3">    
                
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                <td class="table_leftmiddle"></td>
                <td height="280" bgcolor="#FFFFFF" valign="top">

<!-- Informationtexts -->
{foreach name=outer item=item from=$informationtexts}
                    <br>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td valign="top" nowrap><b>{$item.startdate}</b></td>
                            <td width="10"></td>
                            <td>
								{$item.informationtext}                                
                            </td>
                            <td width="20"></td>
                        </tr>
                    </table>
{/foreach}

				</td>
                <td bgcolor="#FFFFFF" valign="top" align="right"><img src="Images/flower.png" width="228" height="137"></td>
                </tr>
                </table>
            
            </td>
                <td class="table_rightmiddle" valign="top"><img src="Images/rightmiddle_flower.png" width="13" height="85"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle" colspan="2"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>