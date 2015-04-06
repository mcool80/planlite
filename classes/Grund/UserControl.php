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
include_once('GrundControl.php');
/**
 * Class UserControl
 * Controls the user actions
 * sets rights, creates and changes user data
 * @author Markus Svensson
 * @version 1.01
 */
class UserControl extends GrundControl {
   /** Local errormesage */
   var $errormsg;
   /** Unit to be displayed */
   var $displayedunit;   
   /** User to be displayed */
   var $displayeduser;
   /** Local message */
   var $message;
   /** Update user 1=update all attributes, 2=update for person and 3=update only for other unit */
   var $updateuser;
   /** Create unit */
   var $createuser;   
   /** All available unit, to be used as other units */
   var $allunits = array();
   /** All available modules for organisation */
   var $modules = array();
   /** List of parents (grandparents) to unit displays */
   var $parentunits = array();

  /**
   * Constructor
   */
   function UserControl()
   {
      $this->currentpagename = 'User';
      $this->addTemplates(array("Grund/userpage.tpl"), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
         
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - createuserpage  Display a create user page (indata createorganisation (if 1 go to createorganisation page after usercrated))
   *   - createuser Creates a user in database (indata head_unitid, username, name, email, phonenumber, color(hexcode), resourceid, default_moduleid)
   *   - edituserpage  Display a edit user page (indata userid, opt. unitid)
   *   - edituser Updates user in database (indata userid, head_unitid, username, name, email, phonenumber, color(hexcode), resourceid, default_moduleid, locked, 'rightname')
   *   - addotherunit Adds a other unit to user to database (indata userid, unitid)
   *   - removeotherunit Removes other unit to user to database (indata userid, unitid) 
   *   - viewuser  Diplay page with information on user
   *   - removeuser  Removes user (indata userid, returl)
   *   - sendmessage Send message to support or admin (indata message)
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      $this->checkLoggedinuser();
      switch ( $action )
      {
         case 'createuserpage':
            $this->createUserpage();
            break;
         case 'createuser':
            $user = null;
            $err = $this->createUser($_POST['head_unitid'], $_POST['username'], $_POST['password'], $_POST['name'],  
                        $_POST['email'], $_POST['phonenumber'], $_POST['color'], 
                        $_POST['resourceid'], $_POST['internal'], $_POST['default_moduleid'], $_POST['locked'], 0,  
                        $_POST['isadmin'],  $_POST['editdata'], $_POST['userright'], $user);
            if ( $err )
            {
               $this->displayError($err, "");
               $this->createUserpage($user);
            }
            else
               header("Location: sida.php?page=user&action=edituserpage&userid=$user->userid");
            break;
         case 'edituserpage':
            $this->editUserpage($_GET['userid'], $_GET['unitid']);
            break;   
         case 'edituser':      
            $err = $this->editUser($_POST['userid'], $_POST['head_unitid'], $_POST['username'], $_POST['password'], $_POST['name'],  
                        $_POST['email'], $_POST['phonenumber'], $_POST['color'], 
                        $_POST['resourceid'], $_POST['internal'], $_POST['default_moduleid'], $_POST['locked'], $_POST['appadmin'],  
                        $_POST['isadmin'],  $_POST['editdata'], $_POST['userright'], $_POST['otherunitid']);
            if ( $err )
            {
               $this->displayError($err, "");
               $this->editUserpage($user->userid, $user->headunit->unitid);
            }
            /* Show edit page for user */
            else
               header("Location: sida.php?page=user&action=edituserpage&userid=".$_POST['userid']."&unitid=".$_POST['otherunitid']);
            break;
         case 'addotherunit':
            /* Check user rights */
            $this->checkUserrightForUnit($_GET['userid'], $_GET['unitid']);
            $user = array_pop($dc->getUser($_GET['userid']));
            $unit = array_pop($dc->getUnit($_GET['unitid']));
            $user->addOtherUnit($unit);
            $err = $dc->updateUser($user);
            if ( $err )
            {
               $this->displayError($err, "");
               $this->editUserpage($user->userid, $user->headunit->unitid);
            }
            else
               header("Location: sida.php?page=user&action=edituserpage&userid=$user->userid&unitid=".$_GET['unitid']);
            break;

         case 'removeotherunit':
            /* Check user rights */
            $this->checkUserrightForUnit($_GET['userid'], $_GET['unitid']);   

            $user = array_pop($dc->getUser($_GET['userid']));
            $user->removeOtherUnit($_GET['unitid']);
            $dc->updateUser($user);
            if ( $_GET['redirecturl'] != '' )
            {
               header("Location: ".$_GET['redirecturl']);
               exit();
            }
            header("Location: sida.php?page=user&action=edituserpage&userid=$user->userid");
            break;         
         case "sendmessage":
            /* Check if it is a user och admin that want to send message */
            $listofemails = array();
            /* Send to admin */
            if ( !$this->loggedinuser->isadmin && !$this->loggedinuser->isaappdmin )
            {
               /* Get all admins for users headunit */
               $users = $dc->getUser("SELECT userid FROM pl_user 
                                      WHERE head_unitid=".$this->loggedinuser->head_unitid." AND isadmin=1");
               foreach ( $users as $u )
                  array_push($listofemails, $u->email);
               $mottagare = "administratör";                  
            }

            /* Send to appadmin */
            if ( $this->loggedinuser->isadmin && !$this->loggedinuser->isaappdmin )
            {
               /* Get all appadmins for server */
               $users = $dc->getUser("SELECT userid FROM pl_user WHERE isappadmin=1");
               foreach ( $users as $u )
                  array_push($listofemails, $u->email);
               $mottagare = "support";
            }

            /* Send mail */
            foreach ( $listofemails as $email )
               $dc->sendMail("Meddelande från planlite.se", $_POST['message'], $this->loggedinuser->email,$this->loggedinuser->name, $email, "");
            $mod = array_pop($dc->getModule($this->loggedinuser->default_moduleid));
            header("Location: ".$mod->defaultpagename."&message=Meddelande har skickats till ".$mottagare.".");
            break;
         case "removeuser":
            $err = $this->removeUser($_GET['userid']);
            if ( $err ) 
            {
               $ec = new ErrorControl($err, "");
               $ec->display();
               exit(0);
            }
            header("Location: ".str_replace('%26', '&', str_replace('%3D', '=', str_replace('%3F', '?',$_GET['returl']))));
            exit();	    
            break;
      }
   }

  /**
   * Remove user from system
   * @param p_userid Id of user
   * @returns 0 if all ok, else errorcode
   */
   function removeUser($p_userid)
   {
      global $dc;
      return $dc->removeUser($p_userid);
   }

  /**
   * Display resources in users displayed unit to smarty object
   * @param smarty Smarty object
   */
   function displayUserUnits(&$smarty)
   {
      /* Add resources */
      $resources = array();
      foreach ( $this->displayedunit->getResources() as $k => $v )
         array_push($resources, get_object_vars($v));            
      $smarty->assign('resources', $resources);
   }
   
  /**
   * Display colors and userrights to smarty object
   * @param smarty Smarty object
   */
   function displayColors(&$smarty)
   {
      global $dc;

      /* Add colors */
      $colors = array();
      for ( $b = 0; $b < hexdec("FF"); $b+=hexdec("40") )         
      {
         $col = dechex($b);
         if ( strlen($col) < 2 ) $col = "0".$col;      
         for ( $g = 0; $g < hexdec("FF"); $g+=hexdec("40") )         
         {      
            $col1 = dechex($g).$col;
            if ( strlen($col1) < 4 ) $col1 = "0".$col1;            
            for ( $r = 0; $r < hexdec("FF"); $r+=hexdec("40") )         
            {         
               $col2 = dechex($r).$col1;         
               if ( strlen($col2) < 6 ) $col2 = "0".$col2;
               array_push($colors, array('color' => $col2));
            }
         }
      }
      $smarty->assign('colors', $colors);
   }   
   
  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();
      $this->displayUser($smarty, $this->loggedinuser);
      $this->displayUnit($smarty, $this->displayedunit);
      $this->displayUnit($smarty, $this->displayeduser->headunit, true, "headunit");
      $this->displayUserUnits($smarty);

      $this->displayUnits($smarty, $this->createunits, 'createunits'); 
      $smarty->assign('createunitssize', sizeof($this->createunits));      
      $this->displayUnits($smarty, $this->allunits, 'allunits'); 
      $smarty->assign('allunitssize', sizeof($this->allunits));      

      $this->displayUser($smarty, $this->displayeduser, false, "displayed", $_GET['unitid']);

      $this->displayColors($smarty);
      $this->displayModules($smarty, $this->modules);   
      $this->displayParentUnits($smarty, $this->parentunits);
      
      $this->displayErrorText($smarty);
      
      $smarty->assign('showmodule', '0');
      $smarty->assign('createuser', $this->createuser);
      $smarty->assign('updateuser', $this->updateuser);
      $smarty->assign('message', $this->message);
     
      $this->displayHeader();
      $this->displayContent($smarty);
      $this->displayTail();
   }
   
  /**
   * Get units which user is admin for
   */
   function getCreateunits()
   {
      $this->createunits = array();
      $this->addSubunits($this->createunits, $this->loggedinuser->headunit);
      $org = $this->loggedinuser->headunit->getOrganisation();
      $this->allunits = array();
      $this->addSubunits($this->allunits, $org->getOrganisationUnit());
   }   
   
  /**
   * Show edit user page
   * @param p_userid Id on user to edit
   * @param p_unitid Id on unit for user to edit rights in
   */
   function editUserpage($p_userid, $p_unitid)
   {
      global $dc;
      $this->displayeduser = array_pop($dc->getUser($p_userid));
      $org = $this->displayeduser->headunit->getOrganisation();
      $this->modules = $org->getModules();
      if ( $p_unitid != '' )
         $this->displayedunit = array_pop($dc->getUnit($_GET['unitid']));
      else
         $this->displayedunit = $this->displayeduser->headunit;
      /* Check user rights */
      if ( $this->loggedinuser->isAdmin($this->displayeduser->headunit->unitid) )
         $this->updateuser = '1';
      elseif ( $this->displayeduser->userid == $this->loggedinuser->userid &&
            $this->loggedinuser->canEditdata()  )
         $this->updateuser = '2';
      elseif ( $this->displayeduser->isAdmin($this->displayedunit->unitid) )
         $this->updateuser = '3';
      else
      {
          $ec = new ErrorControl(10062, "");
          $ec->display();
          exit(0);
      }
               
      $this->getCreateunits();         
      $this->parentunits = $this->getParentUnits($this->displayeduser->headunit);   
      $this->currentsubpagename = 'edituser';      
   }
   
  /**
   * Check if user has right to change unit in user
   * @param p_userid Id on user to edit
   * @param p_unitid Id on unit for user to edit rights in
   */
   function checkUserrightForUnit($p_userid, $p_unitid)
   {
      global $dc;
      $user = array_pop($dc->getUser($p_userid));
      if ( !( $this->loggedinuser->isAdmin($user->headunit->unitid) ||
           ( $user->userid == $this->loggedinuser->userid &&   $this->loggedinuser->canEditdata() ) ||
           $user->isAdmin($p_unitid) ) )
      {
         $ec = new ErrorControl(10062, "");
         $ec->display();
         exit(0);
      }            
   }
   
  /**
   * Set information for create user page
   * @param displayeduser User object that should be displayed
   */
   function createUserpage($displayeduser=null)
   {
      global $dc;
      $this->checkUserAdmin();
      $unitid = $_GET['unitid'];
      if ( $unitid == '' )
         $unitid = $this->loggedinuser->headunit->unitid;
      if ( !$this->loggedinuser->isAdmin($unitid) )
      {
         $ec = new ErrorControl(10062, "");
         $ec->display();
         exit(0);
      }

      $this->displayedunit = array_pop($dc->getUnit($unitid));
      $this->getCreateunits();
      $this->createuser = '1';
      $this->displayeduser = $displayeduser;
      $org = $this->displayedunit->getOrganisation();
      $this->modules = $org->getModules();   
      $this->currentsubpagename = 'createuser';
   }
   
  /** 
   * Create a new user
   * @param p_head_unitid Head unit id
   * @param p_username User name
   * @param p_password Password, unencrypted
   * @param p_name Name
   * @param p_email E-mail
   * @param p_phonenumber Phone number
   * @param p_color Colour
   * @param p_resourceid Id of resource, can be null
   * @param p_internal 1=internal, 0=external, default 1
   * @param p_default_moduleid Id of module
   * @param p_locked 1=locked, 0=unlocked
   * @param p_isappadmin 1 if user is application admin
   * @param p_isadmin 1 if user is admin over head unit
   * @param p_editdata 1 if user can change personal data
   * @param p_userright Userrights to set for user
   * @param p_user User object, created user (out)
   * @returns 0 if all is ok, else errorcode
   */
   function createUser($p_head_unitid, $p_username, $p_password, $p_name,  
                        $p_email, $p_phonenumber, $p_color, 
                        $p_resourceid, $p_internal, $p_default_moduleid, $p_locked, $p_isappadmin,  
                        $p_isadmin, $p_editdata, $p_userright, &$p_user)
   {
      global $dc;
      $p_user = new User(null, $p_head_unitid, $p_username, $p_password, $p_name,  
                         $p_email, $p_phonenumber, $p_color, 
                         $p_resourceid, $p_internal, $p_default_moduleid, $p_locked, $p_isappadmin,  
                         $p_isadmin, $p_editdata);
      $p_user->changePassword($p_password);
      /* Add user rights */
      if ( is_array($p_userright) )
         foreach ( $p_userright as $userrightname )
            $p_user->setRight($p_head_unitid, $userrightname);
      $unit = array_pop($dc->getUnit($p_head_unitid));
      if ( !is_object($unit) )
      {
         return 10071;
      }
	  $org = $unit->getOrganisation();
      if ( !$org->canAdduser() )
      {
         return 10072;
      }            
      $err = $dc->updateUser($p_user, false);
      if ( $err )
      {
         return $err;
      }   
      return 0;
   } 

  /**
   * Edit a user in database
   * @param p_userid User id on user to edit
   * @param p_head_unitid Head unit id
   * @param p_username User name
   * @param p_password Password, unencrypted
   * @param p_name Name
   * @param p_email E-mail
   * @param p_phonenumber Phone number
   * @param p_color Colour
   * @param p_resourceid Id of resource, can be null
   * @param p_internal 1=internal, 0=external, default 1
   * @param p_default_moduleid Id of module
   * @param p_locked 1=locked, 0=unlocked
   * @param p_isappadmin 1 if user is application admin
   * @param p_isadmin 1 if user is admin over head unit
   * @param p_editdata 1 if user can change personal data
   * @param p_userright Userrights to set for user
   * @param p_otherunitid Id of unit that where p_userrights shall be set
   * @returns 0 if all is ok, else errorcode
   */
   function editUser($p_userid, $p_head_unitid, $p_username, $p_password, $p_name,  
                        $p_email, $p_phonenumber, $p_color, 
                        $p_resourceid, $p_internal, $p_default_moduleid, $p_locked, $p_appadmin,  
                        $p_isadmin,  $p_editdata, $p_userright, $p_otherunitid)
   {
      global $dc;
      $typeofedit = "";
      $user = array_pop($dc->getUser($p_userid));
      if ( $this->loggedinuser->isAdmin($user->headunit->unitid) )
         $typeofedit = "ALL";
      elseif ( $user->userid == $this->loggedinuser->userid &&
               $this->loggedinuser->canEditdata() )
         $typeofedit = 'EDITUSER';
      elseif ( $user->isAdmin($p_otherunitid) )
         $typeofedit = 'OTHERUNIT';
      else
      {
         return 10062;
      }    
    
      /* Add data according to user rights */
      if ( $typeofedit == 'ALL' )
      {
         $user->username = $p_username;
         $user->name = $p_name;
         $user->setHeadunit($p_head_unitid);
         $user->default_moduleid = $p_default_moduleid;
         $user->locked = $p_locked;
         $user->isadmin = $p_isadmin;
         $user->editdata = $p_editdata;
         if ( $p_internal == null )
            $p_internal = 0;
         $user->internal = $p_internal;
      }         
      if ( $typeofedit == 'ALL' || $typeofedit == 'EDITUSER' )
      {
         if ( $user->password != $p_password )
            $user->changePassword($p_password);
         $user->email = $p_email;
         $user->phonenumber = $p_phonenumber;
         $user->color = $p_color;
         $user->resource = array_pop($dc->getResource($p_resourceid));
      }   

      if ( $p_otherunitid != '' )
      {
         $user->unsetAllRights($p_otherunitid);
         if ( is_array($p_userright) )
            foreach ( $p_userright as $userrightname )
               $user->setRight($p_otherunitid, $userrightname);
         elseif ( $p_userright != '' )
            $user->setRight($p_otherunitid, $p_userright);
            
      }
      $err = $dc->updateUser($user);
      return $err;
   }
}
?>