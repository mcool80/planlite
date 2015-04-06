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
 
include_once('Usertime.php');

/**
 * Class Activitytime
 * Contains information about plannedtime or/and workedtime for a user
 * @author Markus Svensson
 * @version 1.01
 */
class Activitytime extends Usertime
{
   /** Id of the user connected to the time */
   var $userid;

   /** Id of the activitytime */
   var $activitytimeid;
   /** Planned time for given period */
   var $activitytime;

   /** Planned time */
   var $plannedtime;
   /** Worked time */
   var $workedtime;

   /** Symbol defining the unit for planned and worked time */
   var $unitsymbol;
   
   /** Description field */
   var $description;   

  /**
   * Constructor
   *
   * @param p_activitytimeid Id of this activitytime
   * @param p_activityslotid Id of the activityslot where activitytime exists
   * @param p_userid Id of user
   * @param p_startdate Time when the activity starts for user
   * @param p_plannedtime Planned time
   * @param p_workedtime Worked time
   * @param p_unitsymbol Unit symbol for planned and worked time
   * @returns Activitytime object
   */
   function Activitytime($p_activitytimeid, $p_activityslotid, $p_userid, $p_startdate, $p_plannedtime, $p_workedtime,
                          $p_unitsymbol, $p_description = "")
   {
      $this->activitytimeid = $p_activitytimeid;
      $this->activityslotid = $p_activityslotid;
      $this->userid = $p_userid;
	  /* Remove seconds (swedish time) */
      $this->startdate = $p_startdate;
      if ( strlen($this->startdate) > 18 )
         $this->startdate = substr($this->startdate, 0, -3);
      $this->plannedtime = $p_plannedtime;
      $this->workedtime = $p_workedtime;
      $this->unitsymbol = $p_unitsymbol;
	  $this->description = $p_description;
   }
   
  /**
   * Get user in activitytime
   * @return User object, if no user found false is returned
   */
   function getUser()
   {
       global $dc;
	   $user = array_pop($dc->getUser($this->userid));
	   if ( is_object($user) )
	      return $user;
       return false;
   }
}
?>