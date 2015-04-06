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
 * Class HelpControl
 * Controls the module actions
 * installs and upgrades modules
 * @author Markus Svensson
 * @version 1.01
 * Revisions:
 * #1.01  Markus Svensson       Added title to helptext, and changed to user objects in template
 */
class HelpControl extends GrundControl {
   /** Help that is to be displayed */
   var $help;
   
  /**
   * Constructor
   */
   function HelpControl()
   {
      $this->addTemplates(array("Grund/helppage.tpl"), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - showhelp Display help page (indata pagename, subpagename)
   *
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      switch ( $action ) 
      {
         case "showhelp":
            $this->help = $dc->getHelp($_GET['pagename']);
            break;
      }
   }

  /** 
   * Display help and helptexts to smarty object
   * @param smarty Smarty object
   * @param p_help Help object to be displayed
   */
   function displayHelp(&$smarty, $p_help)
   {
//      foreach ( get_object_vars($p_help) as $k => $v )
//      {
//         $smarty->assign($k, $v);
//      }
      $smarty->assign('help', $p_help);
      $helptexts = array();
      foreach ( $p_help->helptext as $ht )
      {
         array_push($helptexts, $ht);
      }
      $smarty->assign('helptexts', $helptexts);
   } 

  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();
      if ( is_object($this->help) )
         $this->displayHelp($smarty, $this->help);
      else
      {
         $smarty->assign('helptexts', array());
         $smarty->assign('title', 'Hjälp saknas för denna sida');
      }
      $this->displayContent($smarty);
   }
}
?>