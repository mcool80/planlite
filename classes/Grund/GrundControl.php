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
 * Class GrundControl
 * Controls the module actions
 * Shows first page for Grund Module
 * @author Markus Svensson
 * @version 1.00
 */
class GrundControl extends IPageControl {
   /** Local errormesage */
   var $errormsg;
   /** Local message */
   var $message;
   /** Array of informationtext */
   var $informationtexts = array();
   /** Array of module */
   var $modules = array();
   /** Logged in user */
   var $loggedinuser;
   /** Show contact window if 1 */
   var $showcontact;
   /** Current page name */
   var $currentpagename;
   /** Current sub page name */
   var $currentsubpagename;
   /** Content template array */
   var $contenttplarr = array();
   /** Header template array */
   var $headertpl = "";   
   /** Tail template array */
   var $tailtpl = "";
   
  /**
   * Constructor
   */
   function GrundControl()
   {
      $this->currentpagename = 'Grund';
      $this->addTemplates(array("Grund/grundpage.tpl"), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Add templates to display
   * @param p_contenttpl Array with templates that shall be displayed with page content
   * @param p_headertpl Name om template file for header
   * @param p_tailtpl Name om template file for tail
   */
   function addTemplates($p_contenttplarr, $p_headertpl, $p_tailtpl)
   {
      $this->headertpl  = $p_headertpl;
      $this->contenttplarr = $p_contenttplarr;
      $this->tailtpl    = $p_tailtpl;
   }

  /**
   * Add a template filename to content page display
   * @param p_contenttpl Name of content template file
   */
   function addContenttemplate($p_contenttpl)
   {
      array_push($this->contenttplarr, $p_contenttpl);
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - firstpage Shows first page
   *   - about Show about page
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      $this->checkLoggedinuser();
      switch ( $action )
      {
         case 'firstpage':
            $this->firstpage();
            $this->showcontact = '1';
            break;
         case 'about':
            /* TODO: Add this page */
            break;
         default:
            break;
      }
   }

  /**
   * Set display data
   */
   function setdisplaydata(&$smarty)
   {
      $this->displayUnit($smarty, $this->loggedinuser->headunit);            
      $this->displayUser($smarty, $this->loggedinuser);
      $org = $this->loggedinuser->headunit->getOrganisation();
      $this->displayModules($smarty, $org->getModules());      
      $smarty->assign('currentpagename', $this->currentpagename);
      $smarty->assign('currentsubpagename', $this->currentsubpagename);
   }
   
  /**
   * Display header, needs the $this->loggedinuser to be setted
   */
   function displayHeader()
   {
      if ( !is_object($this->loggedinuser) )
         return;   
       
      $smarty = new Smarty();
      $this->setdisplaydata($smarty);
      $smarty->display($this->headertpl);   
   }
   
  /**
   * Display tail, needs the $this->loggedinuser to be setted
   */
   function displayTail()
   {
      if ( !is_object($this->loggedinuser) )
         return;   
       
      $smarty = new Smarty();
      $this->setdisplaydata($smarty);
      $smarty->assign('showcontact', '1');
      $smarty->assign('message', $this->message);
      $smarty->display($this->tailtpl);   
   }
   
  /**
   * Display data in content template files
   */
   function displayContent($smarty)
   {
      foreach ( $this->contenttplarr as $tpl )
         $smarty->display($tpl);
   }
   
  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();

      $this->setdisplaydata($smarty);
      $this->displayInformationtexts($smarty, $this->informationtexts);
      
      $smarty->assign('showcontact', $this->showcontact);
      $smarty->assign('message', $this->message);
           
      $this->displayHeader();
      $this->displayContent($smarty);
      $this->displayTail();
   }

  /**
   * Add modules display to smarty object
   * @param smarty Smarty object
   * @param p_modules Modules to display
   * @param p_prefix Prefix on modules array when displayed
   */      
   function displayModules(&$smarty, $p_modules, $p_prefix="")
   {
      /* Add modules this organisation has */
      $modarr = array();
      foreach ( $p_modules as $module )
      {
         array_push($modarr, get_object_vars($module));
      }
      $smarty->assign($p_prefix.'modules', $modarr);
   }
   
   /**
   * Add informationtext display to smarty object
   * @param smarty Smarty object
   * @param p_informationstexts Informationstexts to be displayed
   * @param p_prefix Prefix on informationtexts array when displayed
   */   
   function displayInformationtexts(&$smarty, $p_informationtexts, $p_prefix="")
   {
      /* Add informationtexts that are to be shown */
      $infotextarr = array();
      foreach ( $p_informationtexts as $infotext )
      {
         array_push($infotextarr, get_object_vars($infotext));
      }
      $smarty->assign($p_prefix.'informationtexts', $infotextarr);   
   }   

  /**
   * Add module display to smarty object
   * @param smarty Smarty object
   * @param p_module Module to be displayed
   * @param p_attributesonly Shown only attributes not objects in Module
   * @param p_prefix Prefix om modules attributes when displayed
   */
   function displayModule(&$smarty, $p_module, $p_attributesonly=false, $p_prefix="")
   {
      /* Add data about user */
      if ( is_object($p_module) )
      {
         $moduledata = get_object_vars($p_module);
         foreach ( $moduledata as $k => $v )
            $smarty->assign($p_prefix.$k, $v);
         if ( $p_attributesonly )
            return;
      }
   }   
   
  /**
   * Add user display to smarty object
   * @param smarty Smarty object
   * @param p_user User to be displayed
   * @param p_attributesonly Shown only attributes not objects in User
   * @param p_prefix Prefix om user attributes when displayed
   * @param p_unitid If not headunit, display information about user in this unit
   */
   function displayUser(&$smarty, $p_user, $p_attributesonly=false, $p_prefix="", $p_unitid="")
   {
      /* Add data about user */
      if ( is_object($p_user) )
      {
         $userdata = get_object_vars($p_user);
         foreach ( $userdata as $k => $v )
            if ( !is_array($v) && !is_object($v) )
               $smarty->assign($p_prefix.$k, $v);
         if ( $p_attributesonly || strlen($p_prefix) == 0 )
            return;
         
         /* Add rights */
         if ( $p_unitid == '' ) 
            $p_unitid = $p_user->head_unitid;
         $userrights = array();

         foreach ( $p_user->getUserrights($p_unitid) as $k => $v )
            array_push($userrights, get_object_vars($v)); 
         $smarty->assign($p_prefix.'userrights', $userrights);

         /* Add other units */
         $otherunits = array();
         foreach ( $p_user->getOtherUnits() as $k => $v )
            array_push($otherunits, get_object_vars($v));            
         $smarty->assign($p_prefix.'otherunits', $otherunits);         
      }
   }

  /**
   * Display parent units to smarty object
   * @param smarty Smarty object
   * @param parentunits Array with Unit, with parentunits to be displayed
   */
   function displayParentUnits(&$smarty, $parentunits)
   {
      $prunits = array();
      foreach ( $parentunits as $k => $v )
         array_push($prunits, get_object_vars($v));
      $smarty->assign('parentunits', $prunits);
   }   

  /** 
   * Display an array of units to smarty object
   * @param smarty Smarty object
   * @param p_units Array of units
   * @param p_attributesonly Show only attributes only, not (sub)objects, default true
   * @param p_displayname Name the array when displayed
   */
   function displayUnits(&$smarty, $p_units, $p_displayname, $p_attributesonly=true)   
   {
      $arr = array();
      foreach ( $p_units as $unit )
      {
         if ( $unit->parentunitid == '' || $unit->parentunitid == null )
            $unit->unitname = $unit->unitname;       
         array_push($arr, get_object_vars($unit));
      }
      $smarty->assign($p_displayname, $arr);
   }

  /** 
   * Display an array of users to smarty object
   * @param smarty Smarty object
   * @param p_users Array of users
   * @param p_displayname Name the array when displayed   
   * @param p_attributesonly Show only attributes only, not (sub)objects, default true
   */
   function displayUsers(&$smarty, $p_users, $p_displayname, $p_attributesonly=true)   
   {
      $arr = array();
      foreach ( $p_users as $user )
      {
         array_push($arr, get_object_vars($user));
      }
      $smarty->assign($p_displayname, $arr);
   }

  /** 
   * Display an array of resources
   * @param smarty
   * @param p_resources Array of resources
   * @param p_displayname Name the array when displayed   
   * @param p_attributesonly Show only attributes only, not (sub)objects, default true
   */
   function displayResources(&$smarty, $p_resources, $p_displayname, $p_attributesonly=true)   
   {
      /* TODO: Merge these simple functions to one function */
      $arr = array();
      foreach ( $p_resources as $resource )
      {
         array_push($arr, get_object_vars($resource));
      }
      $smarty->assign($p_displayname, $arr);
   }   
   
  /**
   * Display unit
   * @param smarty Smarty object
   * @param p_unit Unit to be displayed
   * @param p_attributesonly Shown only attributes not objects in Unit
   * @param p_prefix Prefix om unit attributes when displayed
´  */
   function displayUnit(&$smarty, $p_unit, $p_attributesonly=false, $p_prefix="")
   {
      if ( is_object($p_unit) )
      {
         foreach ( get_object_vars($p_unit) as $k => $v )
            if ( !is_array($v) && !is_object($v) )
               $smarty->assign($p_prefix.$k, $v);

         if ( $p_attributesonly )
            return;
                                             
         /* Add user from other units */
         $otherusers = array();
         foreach ( $p_unit->getOtherUsers() as $k => $v )
         {
            $usr = get_object_vars($v);
            $usr['unitname'] = $v->headunit->unitname;
            array_push($otherusers, $usr);
         }
         $smarty->assign($p_prefix.'otherusers', $otherusers);
         
         /* Add users */
         $users = array();
         foreach ( $p_unit->getUsers() as $k => $v )
         {
            $user = array();
            foreach ( get_object_vars($v) as $k2 => $v2 )
               if ( !is_array($v2) && !is_object($v2) )
                  $user[$k2] = $v2;
            array_push($users, $user);
         }
         $smarty->assign($p_prefix.'users', $users);
                  
         /* Add subunits */
         $subunits = array();
         foreach ( $p_unit->getSubunits() as $k => $v )
            array_push($subunits, get_object_vars($v));         
         $smarty->assign($p_prefix.'subunits', $subunits);
         
         /* Add informationtexts */
         $informationtexts = array();
         foreach ( $p_unit->getInformationtexts() as $k => $v )
            array_push($informationtexts, get_object_vars($v));
         $smarty->assign($p_prefix.'informationtexts', $informationtexts);
                     
         /* Add resources */
         $resources = array();
         foreach ( $p_unit->getResources() as $k => $v )
            array_push($resources, get_object_vars($v));            
         $smarty->assign($p_prefix.'resources', $resources);
      }         
   }   
   
  /**
   * Check if user is logged in, if so the user object is
   * set to $this::loggedinuser
   */
   function checkLoggedinuser()
   {
      global $dc;
      /* Check if user logged in and/or locked */
      $this->loggedinuser = array_pop($dc->getUser("'".$_SESSION['loggedinuserid']."'"));
      if ( $this->loggedinuser == null )
         header("Location: sida.php?page=login&action=loginpage");
      if ( $this->loggedinuser->locked )
         header("Location: sida.php?page=login&action=loginpage");   
   }
   
  /**
   * Check is user is admin, if not display a errorpage
   */
   function checkUserAdmin()
   {
      $this->checkLoggedinuser();
      global $dc;
      if ( !$this->loggedinuser->isAdmin($this->loggedinuser->headunit->unitid) )
      {
          $ec = new ErrorControl(10062, "");
          $ec->display();
          exit(0);
      }
   }   
   
  /**
   * Gets data for user, users headunit, informationtexts, modules
   */
   function firstpage()
   {
      /* Get informationtexts that is shown */
      $this->informationtexts = array();
      foreach ( $this->loggedinuser->headunit->getInformationtexts() as $infotext )
      {
         if ( $infotext->showinformation() )
         {
            $infotext->startdate = substr($infotext->startdate, 0, 10);
            array_push($this->informationtexts, $infotext);
         }
      }
      /* Get modules for organisation */
      $org = $this->loggedinuser->headunit->getOrganisation();
      $this->modules = $org->getModules();
      $this->message = $_GET['message'];
      $this->currentsubpagename = 'firstpage';
   }
   
  /**
   * Save error to a global variable
   * @param p_errorcode Errorcode 
   * @param p_errormsg Errormessage that shall be appended to error
   */
   function displayError($p_errorcode, $p_errormsg)
   {
      $_SESSION['ErrorObject'] = new ErrorControl($p_errorcode, $p_errormsg);
   }
   
  /**
   * Display Error in current page
   * @smart Smarty object
   */
   function displayErrorText(&$smarty)
   {
      if ( is_object($_SESSION['ErrorObject']) ) 
      {
         $error = $_SESSION['ErrorObject']->error;
         if ( is_object($error) )
            $smarty->assign('errortext', $error->errormsg." (".$_SESSION['ErrorObject']->errormsg.")");
         else
            $smarty->assign('errortext', $_SESSION['ErrorObject']->errormsg);
 
         $_SESSION['ErrorObject']  = null;
      }
   }
   
  /**
   * Add subunits to array
   * Adds the given unit and its subunits (+subunits recursive) to given array
   * @param unitarr Array of unit and subunits
   * @param unit Unit with subunits to add to array
   */
   function addSubunits(&$unitarr, $unit)
   {
      array_push($unitarr, $unit);      
      foreach ( $unit->getSubunits() as $subunit )
      {
         $this->addSubunits($unitarr, $subunit);
      }
   }   
   
  /**
   * Get units which user is admin for
   * @param user User
   * @returns Array of Unit, that user is admin for
   */
   function getAdminunits($p_user)
   {
      $units = array();
      $this->addSubunits($units, $p_user->headunit);
      return $units;
   }

  /**
   * Get users which user is admin for
   * @param p_user User
   * @returns Array of users, that user p_user is admin for
   */
   function getAdminusers($p_user)
   {
      $units = array();
      $this->addSubunits($units, $p_user->headunit);
      $users = array();
      foreach ( $units as $u )
         $users = array_merge($users, $u->getUsers());
      return $users;
   }

  /**
   * Get a list of parents (and grandparents) to a unit
   *
   * @param unit Unit to get parents for
   * @returns Array with Unit in sorted order
   */
   function getParentUnits($unit)
   {
      global $dc;
      $unitid = $unit->parentunitid;
      if ( $unitid != '' || $unitid != null || !is_object($unit)  )
      {
         $unit = array_pop($dc->getUnit("'$unitid'"));
         $arr = $this->getParentUnits($unit);
         array_push($arr, $unit);
         return $arr;
      }   
      return array();
   }
}
?>