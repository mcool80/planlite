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
 
include_once ('AbstractPageFactory.class.php');
include_once ('IPageControl.class.php');
include_once ('ErrorControl.php');
include_once ('Grund/LoginControl.php');
include_once ('Grund/GrundControl.php');
include_once ('Grund/HiearkiControl.php');
include_once ('Grund/OrganisationControl.php');
include_once ('Grund/UnitControl.php');
include_once ('Grund/UserControl.php');
include_once ('Grund/ActivityControl.php');
include_once ('Grund/WorktimeControl.php');
include_once ('Grund/ModuleControl.php');
include_once ('Grund/HelpControl.php');

/**
 * Class PageFactory
 * Creates a page control object.
 * @author Markus Svensson
 * @version 1.00
 */
class PageFactory extends AbstractPageFactory {
   var $id;
   var $page;
   function createPage($p_id, $p_page)
   {
      $this->id = $p_id;
      $this->page = $p_page;
      return $this->createObject();
   }

   function createObject()
   {
      switch ($this->page)
      {
         case 'login':
            return new LoginControl('');
            break;
         case 'grund':
            return new GrundControl('');
            break;
         case 'hiearki':
            return new HiearkiControl('');
            break;            
         case 'organisation':
            return new OrganisationControl('');
            break;                     
         case 'unit':
            return new UnitControl('');
            break;      
         case 'user':
            return new UserControl('');
            break;      
         case 'activity':
            return new ActivityControl('');
            break;
         case 'worktime':
            return new WorktimeControl('');
            break;
         case 'module':
            return new ModuleControl('');
            break;
         case 'help':
            return new HelpControl('');
            break;            
         default:
            break;
      }
   }
}
?>