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
 * Class Activity
 * Contains information about activity
 * @author Markus Svensson
 * @version 1.01
 */
class Activity
{
   /** Id of activity in database */
   var $activityid;
   /** Id of unit that holds activity */
   var $unitid;   
   /** Activityname */
   var $activityname;
   /** Description */
   var $description;
   /** Name of costdriver */
   var $costdriver;
   /** Is internal time, true if it is */
   var $isinternaltime;
   /** Time in hours before notify user */
   var $notifytime;
   
  /**
   * Constructor, creates activity
   *
   * @param p_activityid Id Of activity
   * @param p_unitid Id of the unit where the activity is created
   * @param p_activityname Name of the activity
   * @param p_description Description for the activity
   * @param p_costdriver Name of the cost driver for activity
   * @param p_isinternaltime Is the activity paid for internally
   * @param p_notifytime Time before the activity starts participents will be notified
   * @returns Activity object
   */
   function Activity($p_activityid, $p_unitid, $p_activityname, $p_description, $p_costdriver, 
                     $p_isinternaltime, $p_notifytime)
   {
      $this->activityid = $p_activityid;  
      $this->unitid = $p_unitid;   
      $this->activityname = $p_activityname;
      $this->description = $p_description;
      $this->costdriver = $p_costdriver;
      $this->isinternaltime = $p_isinternaltime;
      $this->notifytime = $p_notifytime;   
   }
   
  /**
   * Gets activityslots from activity 
   *
   * @param p_startdate Start date for search of activityslots, default all
   * @param p_stopdate Stop date for search of activityslots, default from startdate to end
   * @returns Array of Activityslot, activityid is key
   */
   function getActivityslots($p_startdate="1900-01-01 00:00:00", $p_stopdate="9999-12-31")
   {
      /* Fetch activityslots from database and add them to activity */
      global $dc;
      return $dc->getActivityslot("SELECT activityslotid FROM pl_activityslot 
                            WHERE activityid=$this->activityid", $p_startdate, $p_stopdate);
   }

  /**
   * Gets activityslots from activity for a given user
   *
   * @param p_startdate Start date for search of activityslots, default all
   * @param p_stopdate Stop date for search of activityslots, default from startdate to end   
   * @returns Array of Activityslot, activityid is key
   */
   function getActivityslotsWithPerson($p_userid, $p_startdate="1900-01-01 00:00:00", $p_stopdate="9999-12-31")
   {
      /* Fetch activityslots from database and add them to activity */
      global $dc;
      return $dc->getActivityslot("SELECT activityslotid FROM pl_activityslot 
                            WHERE activityid=$this->activityid AND activityslotid ( SELECT activityslotid FROM pl_activitytime WHERE userid=$p_userid )", $p_startdate, $p_stopdate);
   }   
   
  /**
   * Gets unit that holds this activity
   *
   * @returns Unit object for this activity
   */
   function getUnit()
   {
      global $dc;
      return array_pop($dc->getUnit($this->unitid));
   }
   
  /**
   * Gets all users that exists in activity
   * NOT IMPLEMENTED
   * @returns Array of User, with userid as key
   */
   function getUsers()
   {
      /* TODO: Implement this function */
   }
}
?>