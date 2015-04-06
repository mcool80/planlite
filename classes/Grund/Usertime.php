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
 * Class Usertime
 * Contains information about usertime
 * @author Markus Svensson
 * @version 1.00
 */
class Usertime
{
   /** Id of the activityslot */
   var $activityslotid;

   /** Activityslot for this time */
   var $activityslot;

   /** Start date/time */
   var $startdate;

  /**
   * Constructor
   */
   function Usertime()
   {
   }
   
  /**
   * Get activityslot that holds this 
   *
   * @returns Activityslot
   */
   function getActivityslot()
   {
   }

  /**
   * Get user connected to this time
   *
   * @returns User
   */
   function getUser()
   {
   }
}
?>