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
 * Class Hiearkiontrol
 * Controls the a explorer like searchtree
 * shows and select unit or user 
 * saves the selected unit or user in sessionvariable 'selectedunitid' or 'selecteduserid' (IMPORTANT)
 * @author Markus Svensson
 * @version 1.01
 */

class HiearkiControl extends GrundControl {
   
   /** Local errormesage */
   var $errormsg;
   /** Local message */
   var $message;
   
   /** Array Selactable related organisations */
   var $organisationnodes = array();
   
   /** Array of data to display */
   var $unitnode;
   
   var $returnobject;
   
   /** Logged in user */
   var $loggedinuser;
   
  /**
   * Gets all related organisation the loggedinuser has rights to se and starts building a searchtree
   */
   function HiearkiControl()
   {
      $this->currentpagename = 'Hieraki';
      $this->addTemplates(array("Grund/hiearki.tpl"), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - showlist Shows the list as it is configured
   *
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      /* Check if user logged in and/or locked */
      $this->loggedinuser = array_pop($dc->getUser($_SESSION['loggedinuserid']));
      if ( $this->loggedinuser == null )
         header("Location: sida.php?page=login&action=loginpage");
      if ( $this->loggedinuser->locked )
         header("Location: sida.php?page=login&action=loginpage");
      switch ( $action )
      {
         case 'showlist':
            /* Get organisations */
            $org = $this->loggedinuser->headunit->getOrganisation();
            $this->unitnode = $org->getOrganisationUnit();

            $this->returnobject = $_GET['returnobject'];
            $this->showusers = $_GET['showusers'];
            if ( $this->showusers != 'no' && $this->showusers != 'false')
               $this->showusers = true;
            else
               $this->showusers = false;
               $this->currentsubpagename = 'showlist';
            break;
      }
   }

  /**
   * Adds nodes to display list
   * @param arr Array with added units and users (in). Expanded array with added units and their subunits (out)
   * @param units Subunits to add
   * @param level Level in hiearki (0=organisation, 1-X=subunits)
   */
   function addNodes(&$arr, $units, $level)
   {
      $unitsize = sizeof($units);
      $i = 0;
      foreach ( $units as $unit )
      {
         $last = 0;
         if ( ++$i == $unitsize )
            $last = 1;
         $newarr = array('level' => $level, 'unitid' => $unit->unitid,
                         'last' => $last, 'unitname' => $unit->unitname );
         array_push($arr, $newarr);
         $this->addNodes($arr, $unit->getSubunits(), $level+1);
         if ( $this->showusers )
            $this->addUserNodes($arr, $unit->getUsers(), $level+1);
      }
   }
   
  /**
   * Adds users to display list
   * @param arr Array with added units and users (in). Expanded array with added users (out)
   * @param users Users to add
   * @param level Level in hiearki (0=organisation, 1-X=subunits)
   */
   function addUserNodes(&$arr, $users, $level)
   {
      foreach ( $users as $user )
      {   
         array_push($arr, array('level' => $level, 'unitname' => str_repeat(" ", $level).$user->name, 
                    'imgopen' => 'people.gif', 'imgclose' => 'people.gif', 
                    'link' => "sida.php?page=user&action=edituserpage&userid=$user->userid" ));
      }
   }   
   
  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();

      /* Add images, name and unitname */
      $arr = array();
      $smarty->assign('link', "sida.php?page=unit&action=editunitpage&unitid=".$this->unitnode->unitid);
      $smarty->assign('unitid', $this->unitnode->unitid);
      $smarty->assign('unitname', $this->unitnode->unitname);
      $smarty->assign('imgopen', 'group.gif');
      $smarty->assign('imgclose', 'group.gif');
      $this->addNodes($arr, $this->unitnode->getSubunits(), 1);
      $smarty->assign('unitdata', $arr);

      $this->displayHeader();
      $this->displayContent($smarty);
      $this->displayTail();
   }
}
?>