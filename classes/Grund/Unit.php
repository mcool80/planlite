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
 
include_once('Organisation.php');
include_once('Planningtype.php');
include_once('Resource.php');

/**
 * Class Unit
 * Contains information about unit
 * @author Markus Svensson
 * @version 1.01
 */
class Unit
{
   /** Id of the unit in database */
   var $unitid;
   /** Id of parent unit, if organisation unit this is null */
   var $parentunitid;
   /** Unitname */
   var $unitname;
   /** Description */
   var $description;
   /** Unittypeid */
   var $unittypeid;
   /** Unittypename */
   var $unittypename;
   /** Smallest timeunit given in hours */
   var $timeunit;
   
   /** Planningtype */
   var $planningtype;
   
   /** Limit when last to change activitys in unit, 0=no limit at all */
   var $hour_limit;
   
   /** Subunits (private) */
   var $subunit = array();
   /** Users (private) */
   var $user = array();
   /** Users from other unit (private) */
   var $otheruser = array();   
   /** Activities (private) */
   var $activity = array();
   /** Informationtexts (private) */
   var $informationtext = array();
   /** Resources (private) */
   var $resource = array();
   
  /**
   * Constructor
   * Gets informationtexts, unittypename and planningtypename from database
   *
   * @param p_unitid Id of Unit
   * @param p_organisationid Id of the organistion
   * @param p_parentunitid Id of the parent unit
   * @param p_unitname Unit name
   * @param p_description Description for the unit
   * @param p_unittypeid Unit type id
   * @param p_planningtypeid Planning type id
   * @param p_hour_limit Hour limit for planning
   * @returns Unit object
   */
   function Unit($p_unitid, $p_organisationid, $p_parentunitid, $p_unitname, $p_description, 
                 $p_unittypeid, $p_planningtypeid, $p_hour_limit)
   {
      global $dc;
      $this->unitid = $p_unitid;
      $this->organisationid = $p_organisationid;
      $this->parentunitid = $p_parentunitid;
      $this->unitname = $p_unitname;
      $this->description = $p_description; 
      $this->unittypeid = $p_unittypeid;
      $this->planningtypeid = $p_planningtypeid;
      $this->hour_limit = $p_hour_limit;      
      
      /* Unittypename from database */
      $this->unittypename = array_pop($dc->getUnittype($this->unittypeid));
      
      /* Planningtypename from database */
      $this->planningtype = array_pop($dc->getPlanningtype($this->planningtypeid));
      
      /* Get informationstexts */
      $this->getInformationtexts();
   }

  /**
   * Get organisation unit exist in
   *
   * @returns Organisation
   */
   function getOrganisation()
   {
      global $dc;
      return array_pop($dc->getOrganisation($this->organisationid));
   }

  /**
   * Get parentunit for this unit
   *
   * @returns Unit, if no parentunit null is returned
   */
   function getParentUnit()
   {
      global $dc;
      return array_pop($dc->getUnit("'".$this->parentunitid."'"));   
   }

  /**
   * Get subunits for this unit
   *
   * @returns Array with Unit
   */
   function getSubunits()
   {
      global $dc;

      /* Fetch from database and add to object */
      $this->subunit = $dc->getUnit("SELECT unitid FROM pl_unit WHERE parentunitid=$this->unitid");

      /* Return */   
      return $this->subunit;   
   }

  /**
   * Get users in Unit
   *
   * @returns Array of User
   */
   function getUsers()
   {
      global $dc;

      /* Fetch from database and add to object */
      $this->user = $dc->getUser("SELECT userid FROM pl_user WHERE head_unitid='$this->unitid'");

      /* Return */   
      return $this->user;      
   }
   
  /**
   * Get users from other Units
   *
   * @returns Array of User
   */
   function getOtherUsers()
   {
      global $dc;

      /* Fetch from database and add to object */
      $this->otheruser = $dc->getUser("SELECT userid FROM pl_otherunit WHERE unitid='$this->unitid'");

      /* Return */   
      return $this->otheruser;      
   }   

  /**
   * Get resourcenames in unit
   *
   * @returns Srray of Resource
   */
   function getResources()
   {
      global $dc;

      /* Fetch from database and add to object */
      $this->resource = $dc->getResourceInUnit($this->unitid);

      /* Return resources */   
      return $this->resource;      
   }

  /**
   * Adds a informationtext to user
   *
   * @param p_informationtext Informationtext to add
   * @returns 0 if all ok else errorcode
   */
   function addResource($p_resource)
   {
      $this->resource[$p_resource->resourceid] = $p_resource;
      return 0;
   }

  /**
   * Remove a informationtext from user
   *
   * @param p_resourceid Id on informationtext to remove
   * @returns 0 if all ok else errorcode
   */
   function removeResource($p_resourceid)
   {
      $newarr = array();
      foreach ( $this->resource as $resource )
         if ( $resource->resourceid != $p_resourceid )
            $newarr[$resource->resourceid] = $resource;
      $this->resource = $newarr;
      return 0;
   }      

  /**
   * Get activities created in Unit
   *
   * @returns Array of Activity
   */
   function getActivitys()
   {
      global $dc;

      /* Fetch from database and add to object */
      $this->activity = $dc->getActivity("SELECT activityid FROM pl_activity WHERE unitid='$this->unitid'");

      /* Return */   
      return $this->activity;         
   }

  /**
   * Get informationtexts in Unit
   *
   * @returns Array of Informationtext
   */
   function getInformationtexts()
   {
      global $dc;

      /* Fetch from database and add to object */
      $this->informationtext = $dc->getInformationtext("SELECT informationtextid FROM pl_informationtext 
                                             WHERE unitid='$this->unitid'");
      /* Return */   
      return $this->informationtext;         
   }

  /**
   * Adds a informationtext to user
   *
   * @param p_informationtext Informationtext to add
   * @returns 0 if all ok else errorcode
   */
   function addInformationtext($p_informationtext)
   {
      $this->informationtext[$p_informationtext->informationtextid] = $p_informationtext;
      return 0;
   }

  /**
   * Remove a informationtext from user
   *
   * @param p_informationtextid Id on informationtext to remove
   * @returns 0 if all ok else errorcode
   */
   function removeInformationtext($p_informationtextid)
   {
      $newarr = array();
      foreach ( $this->informationtext as $informationtext )
         if ( $informationtext->informationtextid != $p_informationtextid )
            $newarr[$informationtext->informationtextid] = $informationtext;
      $this->informationtext = $newarr;
      return 0;
   }   

  /**
   * Get work time this user has in given timespan
   *
   * @param p_startyear Year
   * @param p_stopyear Year   
   * @param p_startmonth Month
   * @param p_stopmonth Month   
   * @param p_startweek Week
   * @param p_stopweek Week   
   * @param p_startday Day
   * @param p_stopday Day         
   */
   function getWorktime($p_startyear=1900, $p_stopyear=9999, $p_startmonth=1, $p_stopmonth=12, 
                   $p_startweek=1, $p_stopweek=53, $p_startday=1, $p_stopday=31)
   {
      global $dc;
      /* Get worktime from parent unit */
      $unit = $this->getParentUnit();
      if ( is_object($unit) )
         $worktimes = $unit->getWorktime($p_startyear, $p_stopyear, $p_startmonth, $p_stopmonth, 
                                         $p_startweek, $p_stopweek, $p_startday, $p_stopday);
      else
         $worktimes = array();
      
      $unitworktimes = $dc->getWorktime("SELECT worktimeid FROM pl_worktime WHERE unitid='$this->unitid' AND
                                 ( year>=$p_startyear AND year<=$p_stopyear ) AND
                                 ( ( month>=$p_startmonth AND month<=$p_stopmonth ) OR
                                   ( month=0 )
                                 ) AND
                                 ( ( day_in_month>=$p_startday AND day_in_month<=$p_stopday ) OR
                                   ( day_in_month=0 )
                                 ) AND
                                 ( ( week>=$p_startweek AND week<=$p_stopweek ) OR
                                   ( week=0 )
                                 )");
      return $unitworktimes + $worktimes;                                 
   }   

  /**
   * Count all users that exists in this unit and this units subunits.
   * @returns Total number of users.
   */
   function getTotNumOfUsers()
   {
      $retnum = 0;
      foreach ( $this->getSubunits() as $sunit )
         $retnum += $sunit->getTotNumOfUsers();
      $retnum += sizeof($this->getUsers());
      return $retnum;
   }
}
?>