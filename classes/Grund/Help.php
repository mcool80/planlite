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
 * Class Help
 * Contains information about help
 * @author Markus Svensson
 * @version 1.00
 */
class Help
{
   /** Page name */
   var $pagename;
   /** Title */
   var $title;
   /** Mail address to support */
   var $supportmail;
   /** Suppliers name */
   var $supplier;
   /** Version of function the help refers to */
   var $version;
   
   /** Help texts user for page */
   var $helptext = array();
   
  /**
   * Constructor
   *
   * @param p_pagename Name of help page
   * @param p_title Title for page
   * @param p_supportmail Mail to support for this page
   * @param p_supplier Supplier of the functions
   * @param p_version Version of the functions
   * @returns Help object
   */
   function Help($p_pagename, $p_title, $p_supportmail, $p_supplier, $p_version)
   {
      $this->pagename = $p_pagename;
      $this->title = $p_title;
      $this->supportmail = $p_supportmail;
      $this->supplier = $p_supplier;
      $this->version = $p_version;
      
      $this->getSubpages();
   }

  /**
   * Gets subpages for the Help
   *
   * @returns Array of Helptext
   */
   function getSubpages()
   {
      global $dc;
      $this->helptext = $dc->getHelptext($this->pagename);
      return $this->helptext;
   }
}
?>