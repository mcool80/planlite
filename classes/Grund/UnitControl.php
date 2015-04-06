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
 * Class UnitControl
 * Controls the unit actions
 * creates and changes unit data
 * @author Markus Svensson
 * @version 1.00
 */
class UnitControl extends GrundControl {
   /** Local errormesage */
   var $errormsg;
   /** Unit to be displayed */
   var $displayedunit;
   /** Local message */
   var $message;
   /** Units logged in users has admin rights */
   var $createunits = array();
   /** Units types for unit [unittypeid]=>unittypename*/
   var $unittypes = array();
   /** Planningtypes */
   var $planningtypes = array();
   /** Update unit */
   var $updateunit;
   /** Create unit */
   var $createunit;   
   /** Parent unit id, used when to create unit then this id is default */
   var $parentunitid;
   /** List of parents (grandparents) to unit displays */
   var $parentunits = array();

  /**
   * Constructor
   */
   function UnitControl()
   {
      $this->currentpagename = 'Unit';
      $this->addTemplates(array("Grund/unitpage.tpl"), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - createunitpage Display a create unit page
   *   - createunit  Creates a unit in database (indata parentunitid, unitname, description, unittypeid och planningtypid)
   *   - editunitpage Display a edit unit page (indata unitid)
   *   - editunit  Updates unit in database (indata unitid, description, unittypeid och planningtypid )
   *   - addinformationtext  Adds informationtext in unit to database (indata unitid, informationtext, startdate and stopdate)
   *   - removeinformationtext Removes informationtext from database (indata informationtextid)
   *   - addresource  Adds a resourcename to unit in database (indata unitid and resourcename)
   *   - removeresource Removes resourcename from database (indata resourceid)
   *   - editresource Updates resourcename in database (indata resourceid, unitid, resourcename)    
   *   - removeunit Removes unit (+subunits and users) from database (indata unitid)
   *   - showunit Display information of the unit (indata unitid)
   *
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      /* TODO: Add some more rights check for data that is created and modified */
      $this->checkUserAdmin();
      switch ( $action )
      {
         case 'createunitpage':
            $this->createUnit();
            break;
         case 'createunit':            
            $unit = new Unit(null, $this->loggedinuser->headunit->organisationid, $_POST['parentunitid'],
                             $_POST['unitname'], $_POST['description'], $_POST['unittypeid'], 
                             $_POST['planningtypeid'], $_POST['hour_limit']);
            $err = $dc->updateUnit($unit, false);
            if ( $err )
            {
               $this->displayError($err, "");
               $this->createUnit();
               return;
            }
            $this->message = "Enhet har skapats";
            $this->editUnit($unit->unitid);
            break;
         /* Show editpage for unit */
         case 'editunitpage':      
            if ( $_GET['unitid'] != '' )
               $this->editUnit($_GET['unitid']);
            break;
         case 'editunit':
            $unit = new Unit($_POST['unitid'], $this->loggedinuser->headunit->organisationid, $_POST['parentunitid'],
                             $_POST['unitname'], $_POST['description'], $_POST['unittypeid'], $_POST['planningtypeid'], 
                             $_POST['hour_limit']);
            $err = $dc->updateUnit($unit);
            if ( $err )
            {
               $this->displayError($err, "");
               $this->editUnit($unit->unitid);
               return;
            }
            $this->message = "Enhet har uppdaterats";
            $this->editUnit($unit->unitid);
            break;
         case 'addinformationtext':
            $infotext = new Informationtext(null, $_GET['unitid'], $_GET['informationtext'], $_GET['startdate'], $_GET['stopdate']);
            $err = $dc->updateInformationtext($infotext, false);
            if ( $err )
               $this->displayError($err, "");
            $this->editUnit($infotext->unitid);
            break;
         case 'removeinformationtext':
            $infotext = array_pop($dc->getInformationtext($_GET['informationtextid']));
            $err = $dc->removeInformationtext($_GET['informationtextid']);
            if ( $err )
               $this->displayError($err, "");
            else
            {
               $unitid = $infotext->unitid;               
               $this->message = "Information borttagen";            
            }
            $this->editUnit($unitid);
            break;            
         case 'addresource':
         case 'editresource':
            $this->editResource($_GET['resourceid'], $_GET['unitid'], $_GET['resourcename'], $res);
            if ( $err )
               $this->displayError($err, "");
            $this->editUnit($_GET['unitid']);
            break;
         case 'removeresource':
            $this->removeResource($_GET['resourceid'], $unitid);
            if ( $err )
               $this->displayError($err, "");
            else
               $this->message = "Resurstyp borttagen";
            $this->editUnit($unitid);
            break;      
         case 'removeunit':
            $unit = array_pop($dc->getUnit($_GET['unitid']));
            $err = $dc->removeUnit($_GET['unitid']);
            if ( $err )
               $this->displayError($err, "");
            else
            {
               $unitid = $unit->parentunitid;               
               $this->message = "Enhet borttagen";      
            }
            $this->editUnit($unitid);
            break;                        
      }
   }
   
  /**
   * Display unittypes
   */
   function displayTypes(&$smarty)
   {
      /* Add unittypes */
      $utypes = array();
      foreach ( $this->unittypes as $k => $v )
         array_push($utypes, array('unittypeid' => $k, 'unittypename' => $v));         
      $smarty->assign('unittypes', $utypes);

      /* Add planningtypes */
      $pltypes = array();
      foreach ( $this->planningtypes as $pl )
         array_push($pltypes, get_object_vars($pl));
      $smarty->assign('planningtypes', $pltypes);
   }

   /**
    * Displays the page
    */
   function display()
   {
      $smarty = new Smarty();
      $this->displayUser($smarty, $this->loggedinuser);
      $this->displayTypes($smarty);      

      $this->displayUnits($smarty, $this->createunits, 'createunits'); 
      $smarty->assign('createunitssize', sizeof($this->createunits));
      $this->displayUnit($smarty, $this->displayedunit);   
      $this->displayParentunits($smarty, $this->parentunits, 'parentunits'); 

      $this->displayErrorText($smarty);      

      $smarty->assign('showmodule', '0');
      $smarty->assign('parentunitid', $this->parentunitid);
      $smarty->assign('createunit', $this->createunit);
      $smarty->assign('updateunit', $this->updateunit);
      $smarty->assign('message', $this->message);
      $smarty->assign('pagenr', $this->pagenr);

      $this->displayHeader();
      $this->displayContent($smarty);
      $this->displayTail();
   }
   
  /**
   * Add subunits to array
   */
   function addSubunits(&$unitarr, $unit)
   {
      if ( is_object($this->displayedunit) )
      {
         if ( $unit->unitid == $this->displayedunit->unitid )
            return;
      }   
      array_push($unitarr, $unit);      
      foreach ( $unit->getSubunits() as $subunit )
      {
         $this->addSubunits($unitarr, $subunit);
      }
   }
   
  /**
   * Gets information to edit unit
   * @param p_unitid id on unit to edit
   */
   function editUnit($p_unitid)
   {
      global $dc;
      $this->displayedunit = array_pop($dc->getUnit($p_unitid));
      $this->parentunitid = $this->displayedunit->parentunitid; 
      $this->createunits = $this->getAdminunits($this->loggedinuser);      
      $this->parentunits = $this->getParentUnits($this->displayedunit);
      $this->unittypes = $dc->getUnittype();
     
      $this->planningtypes = $dc->getPlanningtype();
      $this->updateunit = '1';
      $this->currentsubpagename = 'editunit';      
   }
   
  /**
   * Set up information for create unit page
   */
   function createUnit()
   {
      global $dc;
      $this->createunits = $this->getAdminunits($this->loggedinuser);
      $this->unittypes = $dc->getUnittype();
      $this->planningtypes = $dc->getPlanningtype();
      $this->createunit = '1';
      $this->displayedunit = null;
      $this->parentunitid = $_GET['parentunitid'];
      $unit = array_pop($dc->getUnit($_GET['parentunitid']));
      if ( is_numeric($_GET['parentunitid']) )
         $this->parentunits = $this->getParentUnits($unit);
      array_push($this->parentunits, $unit);
      $this->currentsubpagename = 'createunit';
   }

  /**
   * Create or edit a resource in unit
   * @param p_resourceid
   * @param p_unitid
   * @param p_resourcename
   * @param p_resource The created/edited resource object
   * @returns 0 if all ok, else an error code
   */
   function editResource($p_resourceid, $p_unitid, $p_resourcename, $p_resource)
   {
      global $dc;
      $p_resource = new Resource($p_resourceid, $p_unitid, $p_resourcename);
      $err = 0;
      if ( $p_resourceid == '' )
         $err = $dc->updateResource($p_resource, false);
      else
         $err = $dc->updateResource($p_resource);
      return $err;
   }

  /**
   * Remove resource from unit
   * @param p_resourceid
   * @param p_unitid - out unitid where resourceid was removed from
   * @returns 0 if all is ok, else an error code
   */
   function removeResource($p_resourceid, &$p_unitid)
   {
      global $dc;   
      $res = array_pop($dc->getResource($_GET['resourceid']));
      $p_unitid = $res->unitid;
      $err = $dc->removeResource($_GET['resourceid']);
      return $err;
   }
}
?>