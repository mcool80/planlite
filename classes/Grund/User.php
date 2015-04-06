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

include_once('Workedtime.php');
include_once('Activitytime.php');
include_once('Worktime.php');
include_once('Userright.php');
include_once('Userlogin.php');

/**
 * Class User
 * Contains information about user
 * @author Markus Svensson
 * @version 1.01
 */
class User
{
   /** Id of the User in datbase */
   var $userid;
   /** Username */
   var $username;
   /** Real name */
   var $name;
   /** Password for user */
   var $password;
   /** Email to user */
   var $email;
   /** Phone number to user */
   var $phonenumber;
   /** Color to identify user, in hex(RGB) 'XXXXXX' */
   var $color;
   /** Default module id, that is selected on login */
   var $default_moduleid;
   /** Locked user */
   var $locked;
   /** Is app admin */ 
   var $isappadmin;
   /** Is admin */
   var $isadmin;   
   /** Can edit user data */
   var $editdata;      
   /** Id of headunit for user */
   var $head_unitid;   
   /** Internal resource or not 1=internal, 0=external (null=internal) */
   var $internal;

   /** Headunit for user */
   var $headunit;
   /** Resource */
   var $resource;

   /** Description field */
   var $description;

   /** Rights for user, right['unitid'] = array(Right-objects) (private) */
   var $userright = array();
   /** Planned time for user (private) */
   var $activitytime = array();
   /** Work time for user */
   var $worktime = array();
   /** Other units for user, key is unitid (private) */
   var $otherunits = array();

  /**
   * Constructor
   * Gets resource from database
   * Gets headunit and otherunits from database
   *
   * @param p_userid Id of user
   * @param p_head_unitid Unit id of this user
   * @param p_username User name
   * @param p_password Password encrypted
   * @param p_name Name
   * @param p_email E-mail
   * @param p_phonenumber Phone number
   * @param p_color Colour
   * @param p_resourceid Id of resource if any, else null
   * @param p_internal Internal resource, 1=true, 0=false (default 1)
   * @param p_default_moduleid Default module id
   * @param p_locked 1 if this user account is locked
   * @param p_isappadmin 1 if user is applikcation admin
   * @param p_isadmin 1 if user is admin over its head unit
   * @param p_editdata 1 if user has the right to change its personal data
   * @returns User object
   */
   function User($p_userid, $p_head_unitid, $p_username, $p_password, $p_name, $p_email, $p_phonenumber, $p_color, 
               $p_resourceid, $p_internal, $p_default_moduleid, $p_locked, $p_isappadmin, $p_isadmin, $p_editdata
			   , $p_description = "")
   {
      $this->userid = $p_userid;
      $this->head_unitid = $p_head_unitid;         
      $this->username = $p_username;
      $this->name = $p_name;
      $this->password = $p_password;
      $this->email = $p_email;
      $this->phonenumber = $p_phonenumber;
      $this->color =  $p_color;
      $this->default_moduleid = $p_default_moduleid;
      $this->locked = $p_locked;
      $this->isappadmin = $p_isappadmin;      
      $this->isadmin = $p_isadmin;      
      $this->editdata = $p_editdata;
      $this->resourceid = $p_resourceid;
      $this->internal = $p_internal;
	  $this->description = $p_description;
/*      if ( $p_internal == null )
         $this->internal = 1; */

      global $dc;
      $this->headunit = array_pop($dc->getUnit($p_head_unitid));
      $this->resource = array_pop($dc->getResource("'$p_resourceid'"));   

      $this->getOtherUnits();
      $this->getUserrights($p_head_unitid);
   }

   /**
   * Adds a other unit to user
   *
   * @param p_unit Unit to add
   * @returns 0 if all ok else errorcode
   */
   function addOtherUnit($p_unit)
   {
      if ( $p_unitid != $this->head_unitid )
         $this->otherunits[$p_unit->unitid] = $p_unit;
      return 0;
   }

  /**
   * Remove a other unit from user
   *
   * @param p_unitid Id on unit to remove
   * @returns 0 if all ok else errorcode
   */
   function removeOtherUnit($p_unitid)
   {
      $newarr = array();
      foreach ( $this->otherunits as $unit )
         if ( $unit->unitid != $p_unitid )
            $newarr[$unit->unitid] = $unit;
      $this->otherunits = $newarr;
      return 0;
   }

  /**
   * Get other units for user
   *
   * @returns Array of Unit
   */
   function getOtherUnits()
   {
      global $dc;
      $this->otherunits = $dc->getOtherUnits("'$this->userid'");
      return $this->otherunits;
   }

  /**
   * Get rights for user in a given unit
   *
   * @param p_unitid Id of the unit
   * @returns Array of Userright (key is rightname)
   */
   function getUserrights($p_unitid)
   {
      global $dc;
      if ( sizeof($this->userright) == 0 )
         $this->userright = $dc->getUserright("'$this->userid'");
      if ( array_key_exists($p_unitid, $this->userright) )
         return $this->userright[$p_unitid]; 
      return array();
   }

  /**
   * Get activitytime for User
   *
   * @param p_startdate Start date for search of activitytime, default all
   * @param p_stopdate Stop date for search of activitytime, default from startdate to end
   * @returns Array of Activitytime
   */
   function getActivitytime($p_startdate='1900-01-01', $p_stopdate='9999-12-31')
   {
      global $dc;
      return $dc->getActivitytimeInUser("'$this->userid'", $p_startdate, $p_stopdate);
   }

  /**
   * Get workedtime for User
   *
   * @param p_startdate Start date for search of workedtime, default all
   * @param p_stopdate Stop date for search of workedtime, default from startdate to end
   * @returns Array of Workedtime
   */
   function getWorkedtime($p_startdate='1900-01-01', $p_stopdate='9999-12-31')
   {
      global $dc;
      return $dc->getWorkedtimeInUser("'$this->userid'", $p_startdate, $p_stopdate);   
   }

  /**
   * Get worktime for User
   *
   * @param p_startdate Start date for search of worktime, default all
   * @param p_stopdate Stop date for search of worktime, default from startdate to end
   * @returns Array of Worktime
   */
/*    function getWorktime($p_startdate='1900-01-01', $p_stopdate='9999-12-31')
   {
      global $dc;
      return $dc->getWorktimeInUser("'$this->userid'", $p_startdate, $p_stopdate);      
   }    */
   
  /**
   * Check if user is admin in a given unit
   *
   * @param p_unitid Id of the unit
   * @returns True if user is admin, else false
   */
   function isAdmin($p_unitid)
   {
      /* Check for rights in headunit and parent units */
      global $dc;
      if ( $this->isadmin == '1')
      {
         $unit = array_pop($dc->getUnit($p_unitid));      
         if ( $unit->unitid == $this->headunit->unitid )
            return true;
         while ( !is_nan($unit->parentunitid) && $unit->parentunitid != '' )
         {      
            $unit = array_pop($dc->getUnit($unit->parentunitid));         
            if ( $unit->unitid == $this->headunit->unitid )
               return true;
         }
      }       
      return false;
   }
   
  /**
   * Check if user can edit its own data
   *
   * @return true If user can change data else false
   */
   function canEditdata()
   {
      /* Check for rights in headunit */
      if ( $this->editdata == '1' )
         return true;
      return false;
   }
   
  /**
   * Check if user has a given right in a given unit
   *
   * @param p_unitid Id of the unit
   * @param p_rightname Name of the right
   * @returns True if the user has the right, else false
   */
   function hasRight($p_unitid, $p_rightname)
   {
      $rights = $this->getUserrights($p_unitid);

      if (  $rights[$p_rightname]->setval )
         return true;
      return false;
   }   

  /**
   * Set right for user 
   *
   * @param p_unitid Unit which to set right for
   * @param p_rightname Name of right to set
   * @returns 0 if all ok else errorcode
   */
   function setRight($p_unitid, $p_rightname)
   {
      return $this->setRightValue($p_unitid, $p_rightname, 1);
   }
   
  /**
   * Get units with a given right
   * @param p_rightname Rightname the user has in units
   */
   function getUnitsWithRight($p_rightname)
   {
      global $dc;
      return $dc->getUnit("SELECT unitid FROM pl_right_in_unit 
                           WHERE userid='".$this->userid."' AND rightname='$p_rightname'");
   }
   
  /**
   * Unset right for user 
   *
   * @param p_unitid Unit which to unset right for
   * @param p_rightname Name of right to unset
   * @returns 0 if all ok else errorcode
   */
   function unsetRight($p_unitid, $p_rightname)
   {
      return $this->setRightValue($p_unitid, $p_rightname, 0);
   }

  /** 
   * Unset all rights for user in a given unit
   * 
   * @param p_unitid ID of unit to reset rights for
   * @returns 0 if all ok, else errorcode
   */
   function unsetAllRights($p_unitid)
   {
      $this->userright[$p_unitid] = array();
      return 0;
   }

  /**
   * Set a value for right to user 
   *
   * @param p_unitid Unit which to set right value for
   * @param p_rightname Name of right to set
   * @returns 0 if all ok else errorcode
   */
   function setRightValue($p_unitid, $p_rightname, $p_value)
   {
      if ( array_key_exists($p_unitid, $this->userright) )
      {
         if ( array_key_exists($p_rightname, $this->userright[$p_unitid]) )
         {
            $this->userright[$p_unitid][$p_rightname]->setval = $p_value;
         }
      }
      global $dc;
      $newright = array_pop($dc->getRight("'$p_rightname'"));
      $newright->unitid = $p_unitid;
      $newright->setval = $p_value;
      if ( !array_key_exists($p_unitid, $this->userright) )
         $this->userright[$p_unitid] = array();
      $this->userright[$p_unitid][$p_rightname] = $newright;
      return 0;
   }

  /**
   * Check if the user is locked
   *
   * @returns true if locked else false
   */
   function isLocked()
   {
      if ( $this->locked )
         return true;
      return false;
   }
    
  /**
   * Gets time planned for user in a activityslot
   * 
   * @returns Hours of planned time for user in activityslot
   */
   function getPlannedtimeInActivityslot($p_activityslotid)
   {
   }

  /**
   * Gets time worked for user in a activityslot
   * 
   * @returns Hours of worked time for user in activityslot
   */    
   function getWorkedtimeInActivityslot($p_activityslotid)
   {
   }    
    
  /**
   * Gets time planned for user in a activity
   * 
   * @returns Hours of planned time for user in activity
   */
   function getPlannedtimeInActivity($p_activityid)
   {
   }

  /**
   * Gets time worked for user in a activity
   * 
   * @returns Hours of worked time for user in activity
   */    
   function getWorkedtimeInActivity($p_activityid)
   {
   }       
    
  /**
   * Gets all activities that the user has planned time for
   *
   * @returns Array of Activity, with activityid as key
   */
   function getActivitysWithPlannedtime($p_startdate='1900-01-01', $p_enddate='9999-12-31')
   {
      global $dc;
      $userplanned = $dc->getActivitytime("SELECT activitytimeid FROM pl_activitytime 
                                           WHERE userid='$this->userid'", $p_startdate, $p_enddate);
      $retarr = array();
      $aslotids = "";
      foreach ( $userplanned as $up )
      {
         $aslotids = $aslotids."$up->activityslotid,";
      }
      if ( strlen($aslotids) > 1 )
      {
         $aslotids = substr($aslotids, 0, strlen($aslotids)-1);
         $aslots = $dc->getActivityslot($aslotids);
         foreach ( $aslots as $aslot )
         {
            $activity = $aslot->getActivity();
            $retarr[$activity->activityid] = $activity;
         }
      }
      return $retarr;
    }     

  /**
   * Gets all activities that the user has worked time for
   *
   * @returns Array of Activity, with activityid as key
   */
   function getActivitysWithWorkedtime()
   {
   }     
    
  /**
   * Send message to administrator or support
   * 
   * @returns 0 if all ok, else a errorcode
   */
   function sendMessage($p_message)
   {
      if ( $this->isAdmin($this->headunit->unitid) )
         /* TODO: Send message to support */
         ;
      else if ( !$this->isappadmin )
         /* TODO: Send message to administrator */
         ;
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
      /* Get worktime from unit */
      $worktimes = $this->headunit->getWorktime($p_startyear, $p_stopyear, $p_startmonth, $p_stopmonth, 
                   $p_startweek, $p_stopweek, $p_startday, $p_stopday); 
      $userworktimes = $dc->getWorktime("SELECT worktimeid FROM pl_worktime WHERE userid='$this->userid' AND
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
      return $userworktimes + $worktimes;                                 
   }
   
  /**
   * Get number of succesful user logins since a given date
   * @param p_startdate From when the uns
   * @returns number of unsuccesful logins
   */
   function getUnsuccesfullogins($p_startdate)
   {
      global $dc;
      $userlogins = $dc->getUserlogin($this->userid, $p_startdate);
      $nr = 0;

      foreach ( $userlogins as $ul )
      {
         if ( $ul->success )
            $nr++;
      }
      return $nr;
   }

  /**
   * Add a user login to database and locks user if there are to many unsuccessful logins
   * @param p_success Add success
   */
   function addUserlogin($p_success)
   {
      global $dc;
      $ul = new Userlogin($this->userid, date("Y-m-d H:i:s"), $p_success);
      $dc->updateUserlogin($ul);
      $nr = $this->getUnsuccesfullogins(date("Y-m-d H:i:s"), time()-60*60);
      if ( $nr >= 5 )
      {
         $user->locked = 1;
         $dc->setUer($this);
      }
   }
  /**
   * Set the head unit for the user
   * @param p_unitid New head unit id
   */
   function setHeadunit($p_unitid)
   {
      global $dc;
      $this->head_unitid = $p_unitid;
      $this->headunit = array_pop($dc->getUnit($this->head_unitid));
   }

  /** 
   * Check if password is correct
   * @param losenord Password to match
   * @returns True if passwords match, else false
   */
   function passwordOk($password)
   {
      if ( $this->password == crypt($password, $this->username) )
         return true;
      return false;
   }

  /**
   * Change password
   * @param losenord New password
   */
   function changePassword($password)
   { 
      $this->password = crypt($password, $this->username);
   }   
}
?>