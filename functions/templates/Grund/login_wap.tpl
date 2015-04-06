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
    <title>PlanLite | Logga in</title>
    <link rel="stylesheet" href="Include/planlite_wap.css" type="text/css" />
</head>
<body>
<div>
<b>Planlite | Logga in</b>
</div>
<div>
                    <form action="sida.php?page=login&action=loginuser&wap=1" method="POST" name="submit">
					{if $pageerror eq '1'}
					<span style="color:#FF0000 ">{$message}</span><br/>
					{/if}
					<input type="hidden" name="wap" value="1">
					Användarnamn<br/>
					<input type="text" size="17" name="username"><br/>
					Lösenord<br/>
					<input type="password" size="17" name="password"><br/>
					<input type="submit" value="Logga in" />
                    </form>
</div>
</body>
</html>
