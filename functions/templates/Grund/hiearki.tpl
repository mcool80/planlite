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

{literal}
<script type="text/javascript">
  function writePassline(cnt)
  {
     for (i=0;i<cnt-1;i++)
        document.write('<img src="Images/treemenu/passline.gif" border="0">');
  }
  function writeTieline(last)
  {
     if (last == 1)
	    document.write('<img src="Images/treemenu/endline.gif" border="0">');
     else
	    document.write('<img src="Images/treemenu/tieline.gif" border="0">');		
  }
</script>
{/literal}
<table border="0" cellspacing="0" cellpadding="0" width="520" height="470" align="center">
<tr>
    
    <td valign="top">
    
        <!-- Sidans huvudinnehåll -->
        <table width="600" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">Välj enhet</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="480"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td colspan="3" bgcolor="#FFFFFF" valign="top" height="280" align="left">
   <div onmouseout="extras{$unitid}.style.visibility='hidden';" onmouseover="extras{$unitid}.style.visibility='visible';" style="position:absolute;width:150px;height:16px;">
   <a href="sida.php?page=unit&action=editunitpage&unitid={$unitid}"><img src="Images/treemenu/group.gif" border="0">{$unitname}</a>
   <div id="extras{$unitid}" style="position:absolute;visibility:hidden;width:40px;" >&nbsp;
   <a href="sida.php?page=unit&action=createunitpage&parentunitid={$unitid}"><img src="Images/treemenu/add.png" border="0"></a></div>
   </div>
   <br/>

{foreach name=outer item=item from=$unitdata}
  <script type="text/javascript">
     writePassline({$item.level});
     writeTieline({$item.last});
  </script>
   <div onmouseout="extras{$item.unitid}.style.visibility='hidden';" onmouseover="extras{$item.unitid}.style.visibility='visible';" style="position:absolute;width:150px;height:16px;">
   <a href="sida.php?page=unit&action=editunitpage&unitid={$item.unitid}" ><img src="Images/treemenu/group.gif" border="0">{$item.unitname}</a>

   <div id="extras{$item.unitid}" style="position:absolute;visibility:hidden;width:64px;" >&nbsp;
   <a href="sida.php?page=unit&action=removeunit&unitid={$item.unitid}" onclick="if ( !confirm('Vill du ta bort enhet {$item.unitname} från systemet?') ) return false;"><img src="Images/treemenu/remove.png" border="0"></a>
   <a href="sida.php?page=unit&action=createunitpage&parentunitid={$item.unitid}"><img src="Images/treemenu/add.png" border="0"></a></div>
   </div>
   <br/>
{/foreach}
<noscript>
  <h1>This text will be shown if the browser doesn't support javascript</h1>
</noscript>
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


