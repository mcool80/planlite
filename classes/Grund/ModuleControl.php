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
 * Class ModuleControl
 * Controls the module actions
 * installs and upgrades modules
 * @author Markus Svensson
 * @version 1.01
 */
class ModuleControl extends GrundControl {
   /** Local errormesage */
   var $errormsg;
   /** Module to be displayed */
   var $displayedmodule;
   /** Local message */
   var $message;
   /** install page */
   var $installmodule;
   var $installmodule2;
   var $module;   

  /**
   * Constructor
   */
   function ModuleControl()
   {
      $this->currentpagename = 'Module';
      $this->addTemplates(array("Grund/modulepage.tpl"), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - installmodulepage   Display a install module page
   *   - installmodule Install module to database - (indata modulename, sqlfile)
   *   - installinformationpage  Downloads and reads installationfile, shows information from file (indata modulefile)
   *   - installmodule  Installs the downloaded installfile
   *
   * @param action - requested action
   */
   function execAction($action)
   {
      $this->checkLoggedinuser();
      switch ($action)
      {
         case 'installmodulepage':
            if ( !$this->loggedinuser->isappadmin )
               $this->displayError(10061, "");
            $this->installmodule = '1';
            $this->currentsubpagename = 'installmodule';
            break;
         case 'installmodule':
            global $dc;
			// Unzip file
			$this->unzipinfo = str_replace("\n", "<br/>", shell_exec("unzip ".$_FILES['filename']['tmp_name']." -d ../"));
			// Extract (module)name from filename
            $modulename = substr($_FILES['filename']['name'], 0, strpos($_FILES['filename']['name'], '-'));
			// Run sql install-file
            $sqlfile = "../classes/".$modulename."/install.sql";
            $file = fopen("$sqlfile", "r");
            if ( $file == false )
            {
               $this->displayError(10064, "");
               $this->installmodule = '1';
            }
            else
            {
               $sqldata = fread($file, filesize("$sqlfile"));
			   $sqlarr = split(";", $sqldata);
               fclose($file);
			   foreach ( $sqlarr as $sqlcmd ) 
			   {
			      if ( trim($sqlcmd) != "" )
				  {
                     $dc->runSql($sqlcmd.";");
				  }
			   }
               /* TODO: Test this code */
               $pfdata = $dc->createPageFactoryFile();
               $file = fopen("../classes/PageFactory.class.php", "w");
               if ( $file == false )
                  $this->displayError(10064, "");
               fwrite($file, $pfdata);
               fclose($file);
//               $this->module = new Module('', $modulename, '', '', '','');
               $this->module = $this->getModuleByName($modulename);
               $this->installmodule2 = '1';
            }
            $this->currentsubpagename = 'installmodule';
            break;
      }
   }

  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();
      $this->displayUser($smarty, $this->loggedinuser);      
      $smarty->assign('installmodule', $this->installmodule);
      $smarty->assign('installmodule2', $this->installmodule2);
      $this->displayModule($smarty, $this->module);

      $this->displayErrorText($smarty);

      $smarty->assign('showmodule', '0');
      $smarty->assign('unzipinfo', $this->unzipinfo);	  
      $this->displayHeader();
      $this->displayContent($smarty);
      $this->displayTail();            
   }
   
   function getModuleByName($p_modulename) 
   {
      global $dc;
      return array_pop($dc->getModule("SELECT moduleid FROM pl_module WHERE modulename = '$p_modulename'"));
   }
}
?>