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
{if $showcontact eq '1' and $isappadmin eq '0'}
<!-- Kontakta -->
        <table width="640" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="table_uppermiddle_header1" width="150" nowrap><p class="header">Kontakta&nbsp;{if $isadmin}support{else}administratör{/if}</p></td>
                <td class="table_upperend_header1">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="table_uppermiddle" width="427"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td colspan="3" bgcolor="#FFFFFF">
                    {$message}<br>
                    <form class="nomargin" action="sida.php?page=user&action=sendmessage" method="post">
                        <textarea class="form" cols="100" rows="3" name="message"></textarea>&nbsp;&nbsp;
                        <input type="image" src="Images/button_send.png" width="60" height="20">
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
{/if}        
    </td>
</tr>
</table>

</body>
</html>
