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
 * Class Activityslot
 * Contains information about activityslot
 * (a slot of time where users and resources added to).
 * @author Markus Svensson
 * @version 1.01
 */
class Activityslot
{
   /** Id of activityslot in database */
   var $activityslotid;
   /** Id of activity */
   var $activityid;
   /** Startdate (and time) of activityslot */
   var $startdate;
   /** Stopdate (and time) of activityslot */
   var $stopdate;
   /** Planned time for activityslot in hours */
   var $plannedtime;
   /** Is notified */
   var $isnotified;

   /** Symbol defining the unit for planned time */
   var $unitsymbol;
   
   /** Description field */
   var $description;

   /** Activitytimes for resources */
   var $resourceplannedtime = array();
   /** Activitytimes for users */
   var $userplannedtime = array();

  /**
   * Constructor
   *
   * @param p_activityslotid Id of activityslot
   * @param p_activityid Id of the activity where activityslot exits
   * @param p_startdate Startdate for activityslot
   * @param p_stopdate Stopdate for activityslot
   * @param p_plannedtime Planned timed
   * @param p_isnotified Is the activityslot notified to users
   * @param p_unitsymbol Unit symbol for the planned time
   * @returns Activityslot object
   */
   function Activityslot($p_activityslotid, $p_activityid, $p_startdate, $p_stopdate, $p_plannedtime, $p_isnotified,
                      $p_unitsymbol, $p_description = "")
   {
      $this->activityslotid = $p_activityslotid;
      $this->activityid = $p_activityid;
      $this->startdate = $p_startdate;
	  /* Remove seconds (swedish date) */
      if ( strlen($this->startdate) > 18 )
         $this->startdate = substr($this->startdate, 0, -3);
      $this->stopdate = $p_stopdate;
      if ( strlen($this->stopdate) > 18 )
         $this->stopdate = substr($this->stopdate, 0, -3);
      $this->plannedtime = $p_plannedtime;
      $this->isnotified = $p_isnotified;
      $this->unitsymbol = $p_unitsymbol;
	  $this->description = $p_description;

      global $dc;
      $this->resourceplannedtime = $dc->getResourcePlannedtimeInActivityslot("'$this->activityslotid'");
      $this->userplannedtime = $dc->getActivitytimeInActivityslot("'$this->activityslotid'");
   }

  /** 
   * Get activity for this activityslot
   *
   * @returns Activity
   */
   function getActivity()
   {
      global $dc;
      return array_pop($dc->getActivity($this->activityid));
   }

  /**
   * Get resources that has planned time in activityslot
   *
   * @returns Array with Resource, key resouceid
   */
   function getResourcesWithPlannedtime() 
   {
      /* TODO: Implement this function */
   }

  /**
   * Get users that has time in activityslot
   *
   * @returns Array with User, key userid
   */
   function getUsersWithActivitytime()
   {
      /* TODO: Implement this function */   
   }
  
  /**
   * Get planned times for this activityslot
   *
   * @returns Array of Activitytime, key is activitytimeid
   */
   function getActivitytime()
   {
      global $dc;
      return $dc->getActivitytimeInActivityslot($this->activityslotid);
   }
  /**
   * Get if user is planned in a activityslot
   * @returns true if user exist in activityslot, else false
   */
   function isUserInActivityslot($userid) 
   {
      foreach ( $this->userplannedtime as $user) 
	  {
	     if ( $user->userid == $userid )
		    return true;
      }
	  return false;
   }
   
}
?>