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
 
include_once('DatePlanlite.php');
include_once('GrundControl.php');   
/**
 * Class ActivityControl
 * Controls the activity actions
 * creates, changes an activity
 * @author Markus Svensson
 * @version 1.01
 */
class ActivityControl extends GrundControl {
   /** Local errormessage */
   var $errormsg;
   /** User to be displayed */
   var $displayeduser;
   /** Local message */
   var $message;
   /** Show create activity page, if 1 */
   var $createactivity;
   /** Show edit activity page, if 1 */
   var $editactivity;
   /** Show activity list, if 1 */
   var $showactivitys;
   /** Units logged in users has admin rights */
   var $createunits = array();
   /** Activitys in list  to show */
   var $activitys = array();
   /** Users that can be selected by user */
   var $users = array();
   /** Resource that can be selected by user */
   var $resources = array();   
   
  /**
   * Constructor
   * Defines pagename and adds templates
   */
   function ActivityControl()
   {
      $this->currentpagename = 'Activity';
      $this->addTemplates(array(), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *  - createactivitypage   Display a create activity page
   *  - createactivity  Creates a activity in database (indata activityname, description, costdriver, internaltime, unitid, startdate, stopdate, notifytime, repetitiontype (once, daily, weekly, monthly), times and worktime)
   *  - editactivitypage   Display a edit activity page (indata activityid)
   *  - editactivity  Updates user in database (indata activityname, description, costdriver, internaltime, unitid and notifytime)
   *  - addactivityslot Adds a activityslot (indata activityid, startdate, stopdate, plannedtime and isnotified)
   *  - adduserplanned  Adds a user planned worktime to a activityslot (indata activityslotid, userid, startdate, resourceid and plannedtime)
   *  - adduserworked   Adds a user worked time to a activityslot (indata activityslotid, userid, startdate, and plannedtime)
   *  - removeactivity   Removes activity (indata activityid)
   *  - removeactivityslot Remove activityslot (indata activityslotid)
   *  - removeuserplanned  Remove user planned time (indata activitytimeid)
   *  - removeuserworked  Remove user worked time (indata workedtimeid)
   *  - showactivitys Displays all activities loggedinuser can edit or exists in
   *  - notifyusers Notifys user that the activity is about to start (should be runned every hour)
   *
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      $this->checkLoggedinuser();
      switch ( $action )
      {
         case 'createactivitypage':
            $this->addContenttemplate("Grund/createactivitypage.tpl");
            $this->createunits = $this->getAdminunits($this->loggedinuser);
            $this->currentsubpagename = 'createactivity';
            break;

         case 'createactivity':
            $activity = $this->createActivity($_POST['unitid'], $_POST['activityname'], $_POST['description'], $_POST['costdriver'], $_POST['isinternaltime'], $_POST['notifytime'],
                        $_POST['repeat'], $_POST['startdate'], $_POST['starthour'], $_POST['startmin'], $_POST['stopdate'], $_POST['stophour'], $_POST['stopmin'],
                        $_POST['count'], $_POST['plannedtime'], $_POST['unitsymbol']);

            /* Show edit page for activity */
            $this->message = "Aktivitet har sparats";            
            $this->editActivitypage($activity->activityid);
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;

         case 'editactivitypage':
            $this->editActivitypage($_GET['activityid']);
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;

         case 'editactivity':
            $this->editActivity($_POST['unitid'], $_POST['activityid'], $_POST['activityname'], $_POST['description'], $_POST['costdriver'],
                    $_POST['isinternaltime'], $_POST['notifytime']);
                    
            $activity = array_pop($dc->getActivity($_POST['activityid']));            
            $this->message = "Dina ändringar har sparats";
            $this->editActivitypage($activity->activityid);
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;

         case 'editactivityslot':
            $this->editActivityslot($_GET['activityslotid'], $_GET['plannedtime'], $_GET['startdate'], $_GET['stopdate'], $_POST['unitsymbol']);
            header("Location: sida.php?page=activity&action=editactivitypage&activityid=".$_GET['activityid']);
            break;

         case 'addactivityslot':
            $this->addActivityslot($_POST['activityid'], $_POST['activityslotid'], $_POST['startdate'], $_POST['starthour'], $_POST['startmin'], 
                       $_POST['stopdate'], $_POST['stophour'], $_POST['stopmin'], $_POST['plannedtime'], $_POST['unitsymbol']);
            $activity = array_pop($dc->getActivity($_POST['activityid']));            
            $this->message = "Aktivitet har uppdaterats";
            $this->editActivitypage($activity->activityid);
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;

         case 'removeactivityslot':
            $activityslot = array_pop($dc->getActivityslot($_GET['activityslotid']));
            $activity = $activityslot->getActivity();
            $this->removeActivityslot($_GET['activityslotid']);
            
            $this->message = "Aktivitet har uppdaterats";
            $this->editActivitypage($activity->activityid);   
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;
            
         case 'removeactivity':
            $this->removeActivity($_GET['activityid']);
            $this->message = "Aktivitet har tagits bort";
            $this->showActivitys();  
            $this->addContenttemplate("Grund/showactivityspage.tpl");
            break;
         
         case 'showactivitys':
            $this->showActivitys();
            $this->addContenttemplate("Grund/showactivityspage.tpl");
            break;

         case 'adduserplanned':
            $this->addUserplanned($_GET['activityslotid'], $_GET['userid'], $_GET['plannedtime'], $_GET['resourceid'], $_POST['unitsymbol']);   
            $this->editActivitypage($_GET['activityid']);
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;

         case 'edituserplanned':
            if ( is_numeric($_GET['activitytimeid']) )
               $this->editUserplanned($_GET['activitytimeid'], $_GET['plannedtime'], $_GET['startdate'], $_GET['workedtime'], $_POST['unitsymbol']);
            elseif ( is_numeric($_GET['resourceplannedtimeid']) )
               $this->editResourceplanned($_GET['resourceplannedtimeid'], $_GET['plannedtime'], $_POST['unitsymbol']);            
            $this->editActivitypage($_GET['activityid']);
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;

         case 'adduserworked':
            /* TODO: Implemnent this action */
            break;

         case 'removeuserplanned':
            if ( is_numeric($_GET['activitytimeid']) )
               $this->removeUserplanned($_GET['activitytimeid']);
            elseif ( is_numeric($_GET['resourceplannedtimeid']) )
               $this->removeResourceplanned($_GET['resourceplannedtimeid']);            
            $this->editActivitypage($_GET['activityid']);
            $this->addContenttemplate("Grund/editactivitypage.tpl");
            break;

         case 'removeuserworked':
            /* TODO: Implemnent this action */
            break;
      }
   }
   
  /**
   * Displays activity
   * @param smarty Smarty object (in/out)
   * @param p_activity Activity object
   * @param p_attributesonly Show only attributes in activity object
   * @param p_prefix Prefix used when displayed
   */
   function displayActivity(&$smarty, $p_activity, $p_attributesonly=false, $p_prefix="")
   {
      global $dc;
      /* Add data about activity */
      if ( is_object($p_activity) )
      {
         $activitydata = get_object_vars($p_activity);
         foreach ( $activitydata as $k => $v )
            $smarty->assign($p_prefix.$k, $v);
         if ( $p_attributesonly )
            return;
         
         /* Add activityslot */
         $activityslots = array();
         foreach ( $p_activity->getActivityslots() as $k => $v )
         {
            /* Add activitslots planned for users */
            $actslots = get_object_vars($v);
            $actslots['userplannedtime'] = array();         
            foreach ( $v->userplannedtime as $k2 => $v2 )
            {
               $aslot = get_object_vars($v2);
               $user = array_pop($dc->getUser($v2->userid));
               $aslot['name'] = $user->name;
               array_push($actslots['userplannedtime'], $aslot);               
            }
            /* Add activitslots planned for resources */
            foreach ( $v->resourceplannedtime as $k2 => $v2 )
            {
               $aslot = get_object_vars($v2);
               $resource = array_pop($dc->getResource($v2->resourceid));
               $aslot['name'] = $resource->resourcename;
               array_push($actslots['userplannedtime'], $aslot);               
            }                           
            array_push($activityslots, $actslots);            
         }
         $smarty->assign($p_prefix.'activityslots', $activityslots);
      }
   }
   
  /**
   * Display activitys 
   * @param smarty Smarty object (in/out)
   */
   function displayActivitys(&$smarty)
   {
      $arr = array();
      foreach ( $this->activitys as $v )
         array_push($arr, get_object_vars($v));
      $smarty->assign('activitys', $arr);
   }
   
  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();   
      $this->displayUnits($smarty, $this->createunits, 'createunits'); 
      $smarty->assign('createunitssize', sizeof($this->createunits));   
      
      $this->displayUser($smarty, $this->loggedinuser);      
      $this->displayActivity($smarty, $this->displayedactivity);      
      $this->displayUnits($smarty, $this->activitys, 'activitys'); 
      $this->displayUsers($smarty, $this->users, 'users'); 
      $this->displayResources($smarty, $this->resources, 'resources');      

      $this->displayErrorText($smarty);      
      
      $smarty->assign('showmodule', '0');
      $smarty->assign('message', $this->message);
            
      $this->displayHeader();
      $this->displayContent($smarty);
      $this->displayTail();         
   }
   
   
  /**
   * Create an activity in database 
   * @param p_unitid Id on unit to connect activity to
   * @param p_activityname Name of activity
   * @param p_description Description
   * @param p_costdriver Name of costdriver
   * @param p_isinternaltime Is internal time?
   * @param p_notifytime Time to notify before activity occurs
   * @param p_repeat No of repeats of activity time
   * @param p_startdate First date the activity will occur
   * @param p_starthour Start hour of day of activity
   * @param p_startmin Start minutes of activity
   * @param p_stopdate First date the activity will stop
   * @param p_stophour Hour of day when activity will stop
   * @param p_stopmin Minute the activity will stop
   * @param p_count Number of times the activity time will be repeated
   * @param p_plannedtime Planned time per each repeat
   * @param p_unitsymbol The unit symbol the time is represented with
   * @returns Activity object created
   */
   function createActivity($p_unitid, $p_activityname, $p_description, $p_costdriver, $p_isinternaltime, $p_notifytime,
                           $p_repeat, $p_startdate, $p_starthour, $p_startmin, $p_stopdate, $p_stophour, $p_stopmin,
                           $p_count, $p_plannedtime, $p_unitsymbol)
   {
      global $dc;
      /* Check rights */
      if ( !($this->loggedinuser->hasRight($p_unitid, "ADDACTIVITY") ||
           $this->loggedinuser->isAdmin($p_unitid)) )
      {
          $ec = new ErrorControl(10062, "");
          $ec->display();
          exit(0);
      }
      /* Add costdriver */
      /* TODO: Add function to write cost driver to data base */
  
      /* Create activity */
      $activity = new Activity(null, $p_unitid, $p_activityname, $p_description, $p_costdriver, 
                               $p_isinternaltime, $p_notifytime);

      /* Save to database */
      $err = $dc->updateActivity($activity, false);
      
      if ( $err )
      {
         $this->displayError($err, "");
         return $activity;
      }

      /* Add activityslots */
      $day = 0;
      $week = 0;
      $month = 0;
      
      if ( $p_repeat == 'daily' )
         $day = 1;
      if ( $p_repeat == 'weekly' )
         $week = 1;
      if ( $p_repeat == 'monthly' )
         $month = 1;

      $startdate = $p_startdate." ".$p_starthour.":".$p_startmin;
      $stopdate = $p_stopdate." ".$p_stophour.":".$p_stopmin;            
      $start = new DatePlanlite($p_startdate);
      $stop = new DatePlanlite($p_stopdate);            

      for ( $i = 0; $i < $p_count; $i++ )
      {
         $aslot = new Activityslot(null, $activity->activityid, $startdate, $stopdate, $p_plannedtime, 0, $p_unitsymbol);

         $err = $dc->updateActivityslot($aslot, false);
         
         if ( $err )
            $this->displayError($err, "");               

         if ( $month > 0 )
         {
            $start->addMonth(1);
            $stop->addMonth(1);                  
         }
         if ( $week > 0 ) 
         {
            $start->addDay(7);
            $stop->addDay(7);                  
         }
         if ( $day > 0 )
         {
            $start->addDay(1);
            $stop->addDay(1);                  
         }
         $startdate = $start->getDate()." ".$p_starthour.":".$p_startmin;
         $stopdate = $stop->getDate()." ".$p_stophour.":".$p_stopmin;
      }
      return $activity;
   }
   
  /**
   * Update an edited activity
   * On error the displayError is called
   * @param p_unitid Id of unit with activity
   * @param p_activityid Id of activity to change
   * @param p_activityname New activityname
   * @param p_description New description
   * @param p_costdriver New costdriver
   * @param p_isinternaltime New internal time
   * @param p_notifytime New notifytime
   * @returns 0 if all ok else errorid
   */
   function editActivity($p_unitid, $p_activityid, $p_activityname, $p_description, $p_costdriver,
                    $p_isinternaltime, $p_notifytime)
   {
      global $dc;   
      if ( is_nan($p_unitid) )
      {
         $this->displayError(10063, "Enhet måste väljas");
         return 10063;
      }
         
      if ( ! ($this->loggedinuser->isAdmin($p_unitid) ||
           $this->loggedinuser->hasRight($p_unitid, "ADDACTIVITY")) )   
      {
          $ec = new ErrorControl(10062, "");
          $ec->display();
          exit(0);
      }
      $activity = array_pop($dc->getActivity($p_activityid));
      $activity->unitid = $p_unitid;
      $activity->activityname = $p_activityname;
      $activity->description = $p_description;
      $activity->costdriver = $p_costdriver; 
      $activity->isinternaltime = $p_isinternaltime;
      $activity->notifytime = $p_notifytime;
      $err = $dc->updateActivity($activity);
      if ( $err )
         $this->displayError($err);
      return $err;
   }

  /**
   * Add an activityslot to activity
   * @param p_activityid Id of the activity
   * @param p_activityslotid  Not used
   * @param p_startdate Start date of activity slot
   * @param p_starthour Start hour of the activity slot
   * @param p_startmin Start minute of the activity slot     
   * @param p_stopdate Stop date of activity slot
   * @param p_stophour Stop hour of the activity slot       
   * @param p_stopmin Stop minute of the activity slot 
   * @param p_plannedtime Planned time     
   * @param p_unitsymbol Unit symbol for the planned time 
   * @returns Activityslot that is created is returned
   */
   function addActivityslot($p_activityid, $p_activityslotid, $p_startdate, $p_starthour, $p_startmin, 
                            $p_stopdate, $p_stophour, $p_stopmin, $p_plannedtime, $p_unitsymbol)
   {
      global $dc;   

      $activity = array_pop($dc->getActivity($p_activityid));
      if ( !$this->hasRightToEditActivity($activity) )
      {
          $ec = new ErrorControl(10062, "");
          $ec->display();
          exit(0);
      }      
      $aslot = new Activityslot(null, $p_activityid, $p_startdate." ".$p_starthour.":".$p_startmin, 
                     $p_stopdate." ".$p_stophour.":".$p_stopmin, $p_plannedtime, '0', $p_unitsymbol);
      $err = $dc->updateActivityslot($aslot, false);
      if ( $err )
         $this->displayError($err);
      return $aslot;
   }
   
  /**
   * Remove a activity
   * @param p_activityid Id of activity to remove
   * @returns 0 if all ok else errorid
   */
   function removeActivity($p_activityid)   
   {
      global $dc;   
      if ( is_nan($p_activityid) )
      {
         $this->displayError(10063, "Aktivitet måste väljas");
         return 10063;
      }

      $activity = array_pop($dc->getActivity($p_activityid));
      if ( !is_object($activity) )
      {
         $this->displayError(10063, "Aktivitet hittas inte");
         return 10063; 
      }

      if ( !$this->hasRightToEditActivity($activity) )
      {
          $ec = new ErrorControl(10062, "");
          $ec->display();
          exit(0);
      }
      $err = $dc->removeActivity($p_activityid);   
	  return 0;
   }
   
  /**
   * Remove an activityslot
   * @param p_activityslotid Id of the activityslot to remove
   * @returns 0 if all ok else errorid   
   */
   function removeActivityslot($p_activityslotid)
   {
      global $dc;   
      if ( is_nan($p_activityslotid) )
      {
         $this->displayError(10063, "Aktivitetsdatum måste väljas");
         return 10063;
      }
      
      $activityslot = array_pop($dc->getActivityslot($p_activityslotid));
      if ( !is_object($activityslot) )
      {
         $this->displayError(10063, "Aktivitetsdatum hittas inte");
         return 10063;
      }
         
      $activity = $activityslot->getActivity();
                     
      if ( !$this->hasRightToEditActivity($activity) )
      {
          $ec = new ErrorControl(10062, "");
          $ec->display();
          exit(0);
      }
      $err = $dc->removeActivityslot($p_activityslotid); 
      return $err;
   }

  /**
   * Add a user planning
   * @param p_activityslotid Id of activityslot where planned time will be put
   * @param p_userid User id for the planned time
   * @param p_plannedtime Planned time
   * @param p_resourceid Resource id (only used when user id is null)
   * @param p_unitsymbol Unit symbol for the planned time 
   * @returns 0 if all is ok, else an error code
   */
   function addUserplanned($p_activityslotid, $p_userid, $p_plannedtime, $p_resourceid, $p_unitsymbol)
   {
      global $dc;   
      $activityslot = array_pop($dc->getActivityslot($p_activityslotid));

      if ( is_numeric($p_userid) )
      {
          
          $pltime = new Activitytime('', $p_activityslotid, $p_userid, $activityslot->startdate, $p_plannedtime, 0, $p_unitsymbol);
          if ( !$this->hasRightToEditActivitytime($pltime) )
          {
             $this->displayError(10069, "Du har ej rättighet att ta bort användares planerade tider");                  
             return 10069;
          }  
          $err = $dc->updateActivitytime($pltime, false);
          if ( $err )
          {
            $this->displayError($err, "");
            return $err;
         }
      }
      else if ( is_numeric($p_resourceid) )
      {
         $pltime = new Resourceplannedtime('', $p_activityslotid, $p_resourceid, $p_plannedtime, $p_unitsymbol);
         $activityslot = array_pop($dc->getActivityslot($p_activityslotid));
         $activity = $activityslot->getActivity();
         if ( !$this->hasRightToEditActivity($activity) )
         {
             $this->displayError(10069, "Du har ej rättighet att ta bort planerad tider");                  
             return 10069;
         } 
         $err = $dc->updateResourceplannedtime($pltime, false);
         if ( $err )
            $this->diplayError($err, "");
         return $err;
      }
      return 0;
   }   

  /**
   * Remove planned user time
   * @param p_activitytimeid Id of planned time to remove
   * @returns 0 if all ok else errorid   
   */
   function removeUserplanned($p_activitytimeid)
   {
      global $dc;   
      $activitytime = array_pop($dc->getActivitytime($p_activitytimeid));
      $activityslot = array_pop($dc->getActivityslot($activitytime->activityslotid));
      $activity = $activityslot->getActivity();
      if ( !$this->hasRightToEditActivitytime($activitytime) )
      {
         $this->displayError(10069, "Du har ej rättighet att ta bort planerad tid"); 
         return 10069;
      }  

      $err = $dc->removeActivitytime($p_activitytimeid);   
      if ( $err )
         $this->diplayError($err, "");
	  return $err;
   }
   
  /**
   * Remove planned resource time
   * @param p_resourceplannedtimeid Id of planned time to remove
   * @returns 0 if all ok else errorid   
   */
   function removeResourceplanned($p_resourceplannedtimeid)
   {
      global $dc;   
      $activitytime = array_pop($dc->getResourceplannedtime($p_resourceplannedtimeid));
      $activityslot = array_pop($dc->getActivityslot($activitytime->activityslotid));
      $activity = $activityslot->getActivity();
      if ( !$this->hasRightToEditActivity($activity) )
      {
         $this->displayError(10069, "Du har ej rättighet att ta bort planerad tid"); 
         return 10069;
      } 
  
      $err = $dc->removeResourceplannedtime($p_resourceplannedtimeid);   
      if ( $err )
         $this->diplayError($err, "");
	  return $err;
   }

  /**
   * Checks if current logged in userhas the right to change activity
   * @param p_activity Activity object
   * @returns true if user has right to edit activity, else false
   */
   function hasRightToEditActivity($p_activity)
   {
      if ( $this->loggedinuser->isAdmin($p_activity->unitid) ||
           $this->loggedinuser->hasRight($p_activity->unitid, "ADDACTIVITY") )
      {
         return true;
      }  
      return false;
   }

  /**
   * Checks if current logged in userhas the right to change activity
   * @param p_activitytime Activitytime object
   * @returns true if has the right to edit activitytime, else false
   */
   function hasRightToEditActivitytime($p_activitytime)
   {
      global $dc;
      $activityslot = array_pop($dc->getActivityslot($p_activitytime->activityslotid));
      $activity = $activityslot->getActivity();
   
      if ( $this->loggedinuser->isAdmin($activity->unitid) ||
           $this->loggedinuser->hasRight($activity->unitid, "ADDACTIVITY") ||
           ($this->loggedinuser->hasRight($activity->unitid, "EDITACTIVITY") && $p_activitytime->userid == $this->loggedinuser->userid))
      {
         return true;
      }  
      return false;
   }
   
  /**
   * Changed planned time for user
   * @param p_activitytimeid
   * @param p_plannedtime
   * @param p_plannedtime
   * @param p_startdate
   * @param p_workedtime
   * @param p_unitsymbol
   * @returns 0 if all ok else errorid   
   */
   function editUserplanned($p_activitytimeid, $p_plannedtime, $p_startdate, $p_workedtime, $p_unitsymbol)
   {
      global $dc;
      $activitytime = array_pop($dc->getActivitytime($p_activitytimeid));

      if ( !$this->hasRightToEditActivitytime($activitytime) )
      {
         $this->displayError(10069, "Du har ej rättighet att ändra andra användares planerade tider");                  
         return 10069;
      }  
      $activitytime = array_pop($dc->getActivitytime($p_activitytimeid));
      if ( is_numeric($p_plannedtime) )
         $activitytime->plannedtime = $p_plannedtime;
      if ( is_numeric($p_workedtime) )
         $activitytime->workedtime = $p_workedtime;
      if ( strlen($p_startdate) > 0 )
         $activitytime->startdate = $p_startdate;
      if ( strlen($p_unitsymbol) > 0 )
         $activitytime->unitsymbol = $p_unitsymbol; 
      $err = $dc->updateActivitytime($activitytime);
      if ( $err )
      {
         $this->displayError($err, "Kan inte ändra planerad eller arbetad tid");                  
         return $err;
      }  
	  return $err;
   }
   
  /**
   * Changed planned time for resource 
   * @param p_resourceplannedtimeid
   * @param p_plannedtime
   * @param p_unitsymbol
   * @returns 0 if all ok else errorid
   */
   function editResourceplanned($p_resourceplannedtimeid, $p_plannedtime, $p_unitsymbol)
   {
      global $dc;
      $activitytime = array_pop($dc->getResourceplannedtime($p_resourceplannedtimeid));
      $activityslot = array_pop($dc->getActivityslot($activitytime->activityslotid));
      $activity = $activityslot->getActivity();
      if ( !$this->hasRightToEditActivity($activity) )
      {
         $this->displayError(10069, "Du har ej rättighet att ta bort planerad tid"); 
         return 10069;
      }     
  
      $resourceplannedtime = array_pop($dc->getResourceplannedtime($p_resourceplannedtimeid));
      if ( is_numeric($p_plannedtime) )
         $resourceplannedtime->plannedtime = $p_plannedtime;
      if ( strlen($p_unitsymbol) > 0 )
         $resourceplannedtime->unitsymbol = $p_unitsymbol; 
      $err = $dc->updateResourceplannedtime($resourceplannedtime);   
	  return $err;
   }

  /**
   * Change values for activityslot
   * @param p_activityslotid
   * @param p_plannedtime
   * @param p_startdate
   * @param p_stopdate
   * @param p_unitsymbol
   * @returns 0 if all ok else errorid   
   */
   function editActivityslot($p_activityslotid, $p_plannedtime, $p_startdate = null, $p_stopdate = null, $p_unitsymbol = null)
   {
      global $dc;
      $activityslot = array_pop($dc->getActivityslot($p_activityslotid));
      $activity = $activityslot->getActivity();
      if ( !$this->hasRightToEditActivity($activity) )
      {
         $this->displayError(10069, "Du har ej rättighet att ta bort planerad tid"); 
         return 10069;
      }     
  
      $activityslot = array_pop($dc->getActivityslot($p_activityslotid));
      $activityslot->plannedtime = $p_plannedtime;
      if ( !is_null($p_startdate) )
         $activityslot->startdate = $p_startdate;
      if ( !is_null($p_stopdate) )
         $activityslot->stopdate = $p_stopdate;
      if ( strlen($p_unitsymbol) > 0 )
         $activityslot->unitsymbol = $p_unitsymbol; 
      $err = $dc->updateActivityslot($activityslot);
	  return $err;
   }
   
  /**
   * Show activites logged in user can change
   */
   function showActivitys()
   {
      global $dc;
      $units = array();   
      if ( $this->loggedinuser->isAdmin($this->loggedinuser->headunit->unitid) )
         $this->addSubunits($units, $this->loggedinuser->headunit);
      $unitids = "";
      foreach ( $units as $k => $v )
         $unitids = $unitids."'".$v->unitid."', ";
      $unitids = substr($unitids, 0, -2);
      if ( !$this->loggedinuser->isAdmin($this->loggedinuser->headunit->unitid) )
         $this->activitys = $this->loggedinuser->getActivitysWithPlannedtime();
      else
         $this->activitys = $dc->getActivity("SELECT activityid FROM pl_activity WHERE unitid IN ($unitids)");      
      $this->currentsubpagename = 'showactivitys';
   }
   
  /**      
   * Show page that can edit activity
   * @param p_activityid
   */
   function editActivitypage($p_activityid)
   {
      global $dc;
      $this->createunits = $this->getAdminunits($this->loggedinuser);
      $this->displayedactivity = array_pop($dc->getActivity($p_activityid));
      if ( $this->loggedinuser->isAdmin($this->loggedinuser->headunit->unitid) )
         $this->users = $this->getAdminusers($this->loggedinuser);
      else
         $this->users = array($this->loggedinuser);
      $this->resources = $dc->getResource("SELECT resourceid FROM pl_resource WHERE unitid=".$this->displayedactivity->unitid);
      $this->currentsubpagename = 'editactivity';
   }
  /**
   * Get activity(s) by id
   * @param p_activityid List of ids
   * @returns Array of activity or error code on error
   */
   function getActivity($p_activityid)
   {
      global $dc;
	  if ( $p_activityid == "" ) return array();
	  foreach ( split(",", $p_activityid) as $id )
	     if ( !is_numeric($id) )
		   return 10077;
      return $dc->getActivity($p_activityid);
   }
  /**
   * Get activityslot(s) by id
   * @param p_activityslotid List of ids
   * @returns Array of activityslot or error code in error
   */
   function getActivityslot($p_activityslotid)
   {
      global $dc;
	  if ( $p_activityslotid == "" ) return array();	  
	  foreach ( split(",", $p_activityslotid) as $id )
	     if ( !is_numeric($id) )
		   return 10077;
      return $dc->getActivityslot($p_activityslotid);
   }
   
  /**
   * Get activitytime(s) by id
   * @param p_activitytimeid List of ids
   * @returns Array of activitytime or error code on error
   */   
   function getActivitytime($p_activitytimeid)
   {
      global $dc;
	  if ( $p_activitytimeid == "" ) return array();	  	  
	  foreach ( split(",", $p_activitytimeid) as $id )
	     if ( !is_numeric($id) )
		   return 10077;
      return $dc->getActivitytime($p_activitytimeid);
   }
}
?>