<?php 
/*
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
 */
if ( sizeof($_POST) > 0 ) {
    // Create the config file
	$fp = fopen("../classes/Grund/config.php", "w");
	fwrite($fp, "<?php
   global $"."dbuser, $"."dbname, $"."dbpwd, $"."dbaddr;
   $"."dbuser = \"".$_POST['dbuser']."\";
   $"."dbname = \"".$_POST['dbname']."\";
   $"."dbpwd = \"".$_POST['dbpwd']."\";
   $"."dbaddr = \"".$_POST['dbaddr']."\";
?>");
   fclose($fp);
   $installok = 1;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>PlanLite | Installation</title>
    <link rel="stylesheet" href="Include/planlite.css" type="text/css">
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" width="520" height="470" align="center">
<tr>
    <td valign="top">

        <!-- Logo -->
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner"></td>
                <td class="table_uppermiddle"></td>
                <td class="table_upperrightcorner"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td height="130" width="130" bgcolor="#FFFFFF" valign="top">
                    <img src="Images/logo.png" align="center"><br>
                    <p></p>
                </td>
                <td class="table_rightmiddle"></td>
            </tr>
            <tr>
                <td class="table_lowerleftcorner"></td>
                <td class="table_lowermiddle"></td>
                <td class="table_lowerrightcorner"></td>
            </tr>
        </table>
        
    </td>
    <td valign="top">
    
        <!-- Sidans huvudinnehåll -->
        <table width="340" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="table_upperleftcorner_header1"></td>
                <td class="table_uppermiddle_header1" nowrap><p class="header">Logga in</p></td>
                <td class="table_upperend_header1"></td>
                <td class="table_uppermiddle" width="170"></td>
                <td class="table_upperrightcorner_header1"></td>
            </tr>
            <tr>
                <td class="table_leftmiddle"></td>
                <td colspan="3" bgcolor="#FFFFFF" valign="top" height="115" align="center">

                    <br>
                    <form action="install.php" method="POST">
                        <table border="0" cellspacing="0" cellpadding="0" width="275px">
					<?php if ( $installok == 1 ) { ?>
					<tr><td colspan="3" align="left" width="275px">Inställningarna sparade! <br> <a href="sida.php?page=login&action=loginpage">Logga in</a></td></tr>
					<?php } else { ?>
					    <tr><td colspan="2">Skriv in din konfigration</td></tr>
                        <tr>
                            <td align="left">Databasanvändare:</td>
                            <td align="left"><input type="text" name="dbuser"></td>
                        </tr>
                        <tr>
                            <td align="left">Databasnamn:</td>
                            <td align="left"><input type="text" name="dbname"></td>
                        </tr>						
                        <tr>
                            <td align="left">Lösenord:</td>
                            <td align="left"><input type="text" name="dbpwd"></td>
                        </tr>						
                        <tr>
                            <td align="left">Databasserver:</td>
                            <td align="left"><input type="text" name="dbaddr"></td>
                        </tr>						
                        <tr>
                        <td height="10"></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="right">
                            <input  class="button" type="image" src="Images/button_spara.png" width="60" height="20" onclick="form1.submit();">
                            </td>
                        </tr>
					<?php } ?>
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
        
    </td>
</tr>
</table>

</body>
</html>