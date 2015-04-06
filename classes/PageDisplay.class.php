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
 
include_once('PageFactory.class.php');
include_once('../smarty/libs/Smarty.class.php');

/**
 * Class PageDisplay
 * Fetches the page to display executes the action and displays the page.
 * @author Markus Svensson
 * @version 1.00
 */
class PageDisplay 
{
   var $id;
   var $page;
   var $action;
   var $pageobj = null;

  /**
   * Create a PageDisplay object
   * @param p_id
   * @param p_showpage
   * @param p_editpage
   * @param p_page
   * @param p_login
   * @param p_action
   */
   function PageDisplay($p_page, $p_action)
   {
      $this->page = $p_page;
      $this->action = $p_action;
   }

  /**
   * Get the correct page object to display
   */
   function getPage()
   {
      $pfactory = new PageFactory();
      return $pfactory->createPage($this->id, $this->page);
   }

  /**
   * Display the requested page
   */
   function display()
   {
      $this->pageobj = $this->getPage();
      $this->pageobj->execAction($this->action);
      $this->pageobj->display();
   }
}
?>