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
 
/**
 * Class Userlogin
 * Contains information about a userlogin
 * @author Markus Svensson
 * @version 1.00
 */
class Userlogin
{
   /** Id of the user connected to the time */
   var $userid;

   /** Date when userlogin occured */
   var $date;
   
   /** 1=success, 0=unsuccessful login */
   var $success;
   
  /**
   * Constructor
   *
   * @param p_userid Id of user
   * @param p_date Date when log in occured
   * @param p_success 1 if success else 0
   * @returns 
   */
   function Userlogin($p_userid, $p_date, $p_success)
   {
      $this->userid = $p_userid;
      $this->date = $p_date;
      $this->success = $p_success;
   }
}
?>