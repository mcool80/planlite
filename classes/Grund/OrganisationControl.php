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
include_once ('UserControl.php'); 
/**
 * Class OrganisationControl
 * Controls the organisation actions
 * creates and changes unit data
 * @author Markus Svensson
 * @version 1.01
 */
class OrganisationControl extends GrundControl {
   /** Local errormesage */
   var $errormsg;
   /** Organisation to be displayed */
   var $displayedorganisation;
   /** Local message */
   var $message;
   /** Adminuser */
   var $adminuser;
   /** Users */
   var $users = array();
   
   /** Organisationtypes array of string (key is id) */
   var $organisationtypes = array();
   
   /** Show create organisation page */
   var $createorganisation;

   /** Show edit organisation page */
   var $editorganisation;   
   
   /** All organisations to be displayed */
   var $organisations = array();
   
   /** Show a list with organistions in system */
   var $showorganisations;
   
  /**
   * Constructor
   */
   function OrganisationControl()
   {
      $this->currentpagename = 'Organisation';
      $this->addTemplates(array(), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - createorganisationpage  Display a create organisation page
   *   - addadminpage Display a create adminpage and return to createorganisationpage later
   *   - createorganisation Create organisation with given data (indata organisationname, description, userid, no_users, moduleid[X], address, zipcode, phonenumbers, contact, organisationtypeid and relatedorganisation[X])
   *   - editorganisationpage  Dispays a edit organisation page (indata organisationid)
   *   - editorganisation  Edits organisation in database (indata organisationid, organisationname, description, userid, no_users, moduleid[X], address, zipcode, phonenumbers, contact, organisationtypeid and relatedorganisation[X])
   *   - removeorganisation Removes organisation from database (indata organisationid)
   *
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      /* Check if user logged in and/or locked */
      $this->checkLoggedinuser();
      $this->modules = $dc->getModule("SELECT moduleid FROM pl_module");
      switch ( $action )
      {
         case 'createorganisationpage':
            if ( is_object($_SESSION['neworganisation']) )
               $this->displayedorganisation =  $_SESSION['neworganisation'];
            else
               $this->displayedorganisation = new Organisation(null, "", "", "", "", 
                     "", "", "", "", "0");
            $_SESSION['neworganisation'] = $this->displayedorganisation;
            $this->getOrganisationtypes();
            $this->createorganisation = '1';
            $this->currentsubpagename = 'createorganisation';
            $this->addContenttemplate("Grund/orgpage.tpl");
            break;
         case 'createorganisation':
            $err = $this->createOrganisation($_POST['organisationid'], $_POST['organisationname'], 
                                    $_POST['description'], $_POST['no_users'], $_POST['address'], 
                                    $_POST['zipcode'], $_POST['city'], $_POST['contact'], 
                                    $_POST['phonenumbers'], $_POST['organisationtypeid'], $_POST['moduleid'], $org);

            if ( $err )
            {
               $this->displayError($err, "");
               $this->displayedorganisation = $org;
               $_SESSION['neworganisation'] = $this->displayedorganisation;
               $this->getOrganisationtypes();
               if ( !is_numeric($_POST['organisationid']) )
                  $this->createorganisation = '1';
               else
                  $this->editorganisation = 1;
               $this->addContenttemplate("Grund/orgpage.tpl");
               
            }
            else
            {
               if ( !is_numeric($_POST['organisationid']) )
                  header("Location: sida.php?page=organisation&action=editorganisationpage&organisationid=".$org->organisationid."&message=Organisationen har lagts till");
               else
                  header("Location: sida.php?page=organisation&action=editorganisationpage&organisationid=".$_POST['organisationid']."&message=Organisationen har uppdateras");
            }
            break;   
         case 'editorganisationpage':
            $this->editOrganisation();
            $this->addContenttemplate("Grund/orgpage.tpl");
            break;
         case 'showorganisations':
            $this->addContenttemplate("Grund/orglistpage.tpl");
            $this->organisations = $dc->getOrganisation("SELECT organisationid FROM pl_organisation");
            $this->currentsubpagename = 'showorganisations';
            break;
         case 'addrelatedorganisation':
            $this->editorganisation = 1;
            $this->getOrganisationtypes();
            $organisationid = $_GET['organisationid'];
            $relatedorganistionid = $_GET['relatedorganisationid'];
            $err = $dc->updateRelatedorganisation($organisationid, $relatedorganistionid);
            if ( $err )
            {
               $this->displayError($err, "");
               $this->editOrganisation();               
            }
            else
               header("Location: sida.php?page=organisation&action=editorganisationpage&organisationid=$organisationid");
            break;
         case 'removerelatedorganistion':
            $this->editorganisation = 1;
            $this->getOrganisationtypes();         
            $organisationid = $_GET['organisationid'];
            $relatedorganistionid = $_GET['relatedorganisationid'];
            $err = $dc->removeRelatedorganisation($organisationid, $relatedorganistionid);
            if ( $err )
            {
               $this->displayError($err, "");
               $this->editOrganisation();               
            }
            else
               header("Location: sida.php?page=organisation&action=editorganisationpage&organisationid=$organisationid");         
            break;
         case 'removeorganisation':
             $err = $dc->removeOrganisation($_GET['organisationid']);
             if ( $err ) 
                $this->displayError($err, "");
             $this->addContenttemplate("Grund/orglistpage.tpl");
             $this->organisations = $dc->getOrganisation("SELECT organisationid FROM pl_organisation");
             $this->currentsubpagename = 'showorganisations';         
             break;
      }
   }
   
  /**
   * Displays organisationdata to smarty object
   * @param smarty Smarty object
   */
   function displayOrganisation(&$smarty)
   {
      global $dc;
      $allorgs = $dc->getOrganisation("SELECT organisationid FROM pl_organisation 
                                       WHERE organisationid!='".$this->displayedorganisation->organisationid."'");

      if ( is_object($this->displayedorganisation) )
      {
         foreach( get_object_vars($this->displayedorganisation) as $k => $v )
            $smarty->assign($k, $v);
         $relorg = array();
         $this->displayedorganisation->getRelatedOrganisation();
         foreach ( $this->displayedorganisation->relatedorganisation as $v )
            array_push($relorg, get_object_vars($v));
         $smarty->assign('relatedorganisation', $relorg);
         $orgmodules = array();
         foreach ( $this->displayedorganisation->getModules() as $mod )
            array_push($orgmodules, get_object_vars($mod));
         $smarty->assign('orgmodules', $orgmodules);
      }
   } 
   
  /**
   * Display organisationtypes to smarty object
   * @param smarty Smarty object
   */
   function displayOrganisationtypes(&$smarty)
   {
      $orgtype = array();
      foreach ( $this->organisationtypes as $k => $v )
         array_push($orgtype, array('organisationtypeid' => $k, 'typename' => $v));
      $smarty->assign('orgtype', $orgtype);       
   }
   
  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();
      $this->displayUser($smarty, $this->loggedinuser);
      $this->displayOrganisation($smarty);
      $this->displayOrganisations($smarty, $this->organisations);
      $this->displayOrganisationtypes($smarty);
      $this->displayModules($smarty, $this->modules);      
      
      $this->displayErrorText($smarty);
         
      $smarty->assign('showmodule', '0');
      $smarty->assign('createorganisation', $this->createorganisation);
      $smarty->assign('editorganisation', $this->editorganisation);
      $smarty->assign('showorganisations', $this->showorganisations);
      $smarty->assign('message', $this->message);
      
      $this->displayHeader();
      $this->displayContent($smarty);
      $this->displayTail();   
   }
   
  /**
   * Displays an array of organistionsto smarty object
   * @param smarty Smarty object
   * @param p_organisations Array of organisation to display
   * @param p_prefix Prefix on array to display
   */
   function displayOrganisations(&$smarty, $p_organistions, $p_prefix="")
    {
       /* Add informationtexts that are to be shown */
      $organisations = array();
      foreach ( $p_organistions as $org )
      {
         array_push($organisations, get_object_vars($org));
      }
      $smarty->assign($p_prefix.'organisations', $organisations);         
    }
   
  /**
   * Get organisationtypes 
   */
   function getOrganisationtypes()
   {
      global $dc;
      $this->organisationtypes = $dc->getOrganisationtype("SELECT organisationtypeid FROM pl_organisationtype");
   }

  /**
   * Check if user is logged in 
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

      if ( !$this->loggedinuser->isappadmin )
      {
         $errorctrl = new ErrorControl(10061, "");
         $errorctrl->display();
         end();
      }
   }

  /**
   * Set information to edit organisation
   */
   function editOrganisation()
   {
      global $dc;
      $this->editorganisation = 1;
      $this->message = $_GET['message'];
      $this->displayedorganisation = array_pop($dc->getOrganisation($_GET['organisationid']));
      $this->organisations = $dc->getOrganisation("SELECT organisationid FROM pl_organisation");
      $this->getOrganisationtypes();
      $this->currentsubpagename = 'editorganisation';   
   }

  /**
   * Create a organisation
   *
   * @returns 0 if all ok, else errorcode
   */   
   function createOrganisation($p_organisationid, $p_organisationname, $p_description, $p_no_users,
                               $p_address, $p_zipcode, $p_city, $p_contact, $p_phonenumbers, 
                               $p_organisationtypeid, $p_moduleid, &$p_organisation)
   {
      global $dc;
      $neworg = true;
      if ( is_numeric($p_organisationid) )
         $neworg = false;
      $p_organisation = new Organisation($p_organisationid, $p_organisationname, 
                                    $p_description, $p_no_users, $p_address, 
                                    $p_zipcode, $p_city, $p_contact, 
                                    $p_phonenumbers, $p_organisationtypeid);
      $ids = "";
      if ( ! (is_array($p_moduleid) || is_numeric($p_moduleid)) )
         return 10076;
      $moduleids = array();
      if ( is_numeric($p_moduleid) )
      {
         $moduleids  = array($p_moduleid);
         $p_organisation->addModule($dc->getModule($p_moduleid));
      }
      else
      {
         foreach ( $p_moduleid as $moduleid )
            $p_organisation->addModule($dc->getModule($moduleid));
         $moduleids  = $p_moduleid;
      }

      $err = $dc->updateOrganisation($p_organisation, !$neworg);
         
      if ( !$err ) 
         $err = $dc->updateModulesInOrganisation($p_organisation->organisationid, $moduleids);
            
      if ( $err )
         return $err;

      if ( $neworg )
      {
         $unit = $p_organisation->getOrganisationUnit();
         if ( !is_object($unit) )
         {
            return 10072;
         }
         $uc = new UserControl();
         $err = $uc->createUser( $unit->unitid, $_POST['admin'], $_POST['password'], 
                                 'Administratör '.$_POST['organisationname'],  '', '', 'FFFFFF', 
                                 '', '1', array_pop($moduleids), 0, 0, 1, 1, array(), &$admin);			   
      }
      return 0;
   }
}
?>