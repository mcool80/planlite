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
 
// To store member in session it must be declared prior to session_start()
include_once('../classes/Grund/DatabaseControl.php');
include_once('../classes/ErrorControl.php');
session_start();
include_once('../classes/PageDisplay.class.php');

global $dc;
global $sqlcnt, $sqlrader, $hits;
$sqlcnt = 0;
$hits = 0;
$sqlrader = array();
$dc = new DatabaseControl();

$pd = new PageDisplay($_GET['page'], $_GET['action']);

$pd->display();


?>